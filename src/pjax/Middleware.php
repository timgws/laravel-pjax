<?php namespace timgws\pjax;

class Middleware
{
    use PjaxChecksTrait;

    public function handle($request, Closure $next)
    {
        // Get the initial URL that was requested
        $initial_url = $request->getRequestUri();

        // Get the response
        $response = $next($request);

        /*
         * Don't bother handling the request with this middleware if it
         * is not a pjax request, or if the page is not a redirection.
         */
        if (!$request->pjax() || $response->isRedirection()) {
            return $response;
        }

        /**
         * Check that the headers that were sent are OK
         */
        if (!$this->isValidPjaxRequest($request)) {
            return $this->invalidRequest();
        }

        /**
         * Replace the full HTML content with the extracted pjax-only content
         */
        $response->setContent(
            $this->getContent($response)
        );

        // Check if the response was redirected. If it was, send X-PJAX-URL header
        $current_url = $request->getRequestUri();
        if ($current_url !== $initial_url) {
            $response->header('X-PJAX-URL', $current_url);
        }

        return $response;
    }

    /**
     * Return a 409 (Conflict) error code.
     *
     * This is returned when an expection is not met, such as a valid container.
     */
    private function invalidRequest()
    {
        return abort(409);
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

        // Import the response into a DOMDocument, extract the pjax response
        $d = new DOMDocument();
        $d->loadHTML($content);
        $xpath = new DOMXPath($d);
        $xpath_elements = $xpath->query($this->container_xpath);

        /**
         * Ensure that the pjax response could be extracted, and that there is not > 1 item
         */
        if (is_null($xpath_elements) || $xpath_elements->length !== 0) {
            return $this->invalidRequest();
        }

        /**
         * Create a new HTML document with only the extracted content
         */
        $doc = new DOMDocument;
        $doc->preserveWhiteSpace = false;
        $doc->importNode($xpath_elements[0]);

        // and return!
        return $doc->loadHTML($doc);
    }
}