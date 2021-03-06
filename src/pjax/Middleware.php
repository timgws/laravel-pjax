<?php namespace timgws\pjax;

use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Closure;

class Middleware
{
    use PjaxChecksTrait;

    /**
     * Handle the incoming request
     *
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\Response|void
     */
    public function handle($request, Closure $next)
    {
        // Get the initial URL that was requested
        $initial_url = $request->getRequestUri();

        // Get the response
        /** @var Response $response */
        $response = $next($request);

        return $this->createResponse($request, $response, $initial_url);
    }

    /**
     * Return the response that should be created by the middleware
     *
     * @param $request
     * @param Response $response
     * @param $initial_url
     * @return Response|void
     */
    private function createResponse($request, $response, $initial_url)
    {
        /**
         * Don't bother handling the request with this middleware if it
         * is not a pjax request, or if the page is not a redirection.
         */
        if ($this->shouldReturnContent($request, $response)) {
            return $response;
        }

        /**
         * Check that the headers that were sent are OK
         */
        if (!$this->isValidPjaxRequest($request)) {
            return $this->invalidRequest();
        }

        return $this->replaceContent($request, $response, $initial_url);
    }

    /**
     * Extract the HTML from the container that has been updated.
     *
     * @param Request $request
     * @param Response $response
     * @param $initial_url
     * @return \Illuminate\Http\Response
     */
    private function replaceContent($request, $response, $initial_url)
    {
        /**
         * Replace the full HTML content with the extracted pjax-only content
         */
        $response->setContent(
            $this->getContent($response)
        );

        // Check if the response was redirected. If it was, send X-PJAX-URL header
        $current_url = $request->getRequestUri();
        if ($response->isRedirection()) {
            $newUrl = $response->headers->get('Location');
            $response->header('X-PJAX-URL', $newUrl);
        } elseif ($current_url !== $initial_url) {
            $response->header('X-PJAX-URL', $current_url);
        }

        // Send the PJAX layout version if it has been set in the config file
        return $this->setPjaxLayoutVersion($response);
    }

    /**
     * Return a 409 (Conflict) error code.
     *
     * This is returned when an expectation is not met, such as a valid container.
     */
    private function invalidRequest()
    {
        return abort(409);
    }

    /**
     * Check if the configuration has debug mode enabled
     *
     * @return bool
     */
    private function debugMode()
    {
        return config('pjax.debug') === true;
    }

    /**
     * Get only the content we care about
     *
     * @param Response $response
     * @return string html content to send.
     */
    private function getContent(Response $response)
    {
        // Get the full response
        $content = $response->getContent();

        // Get the XML document
        $document = $this->getDocument($content);

        /**
         * Create a new HTML document with only the extracted content
         */
        return $this->getTitle($document) . $this->getNewDocumentContent($document);
    }

    private function getTitle($document)
    {
        $xpath_titles = $this->getDocumentElements($document, '//title');

        if ($xpath_titles->length > 0) {
            return '<title>' . trim($this->getInnerHTML($xpath_titles[0])) . '</title>';
        }

        return '';
    }

    /**
     * @param $document
     * @return string|void
     */
    private function getNewDocumentContent($document)
    {
        // Get the XPath elements
        $xpath_elements = $this->getDocumentElements($document, $this->container_xpath);

        /**
         * Ensure that the pjax response could be extracted, and that there is not > 1 item
         */
        if (is_null($xpath_elements) || $xpath_elements->length !== 1) {
            return $this->htmlForElementsNotMatching($document);
        }

        return $this->getInnerHTML($xpath_elements[0]);
    }

    private function blankHTML()
    {
        return '<!DOCTYPE html><meta charset="utf-8">' .
                '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    }

    /**
     * @param $content
     * @return array
     */
    private function getDocument($content)
    {
        // enable libxml internal errors, avoid php dying on invalid tags
        $use_errors_old_value = libxml_use_internal_errors(true);

        // Import the response into a DOMDocument, extract the pjax response
        $document = new \DOMDocument();
        $document->loadHTML($this->blankHTML() . $content);
        libxml_clear_errors();

        // set libxml internal errors to it's previous value
        libxml_use_internal_errors($use_errors_old_value);

        return $document;
    }

    /**
     * get the elements with the provided container name
     *
     * @param $document
     * @return \DOMNodeList
     */
    private function getDocumentElements($document, $xpath)
    {
        $xpath_query = new \DOMXPath($document);
        $xpath_elements = $xpath_query->query($xpath);

        return $xpath_elements;
    }

    /**
     * @param $document
     * @return string|void
     */
    private function htmlForElementsNotMatching($document)
    {
        if ($this->debugMode()) {
            $xpath_elements = $this->getDocumentElements($document, '//html');
            $html = '';

            foreach ($xpath_elements as $element) {
                $html .= $this->getInnerHTML($element);
            }

            return $html;
        }

        return $this->invalidRequest();
    }

    /**
     * @param $element
     * @return string
     */
    private function getInnerHTML($element)
    {
        $children = $element->childNodes;
        $innerHTML = '';

        foreach ($children as $child) {
            $tmp_doc = new \DOMDocument();
            $tmp_doc->appendChild($tmp_doc->importNode($child, true));
            $innerHTML .= $tmp_doc->saveHTML();
        }

        return $innerHTML;
    }
}
