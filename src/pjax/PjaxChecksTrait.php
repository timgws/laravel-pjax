<?php namespace timgws\pjax;


use timgws\pjax\Exceptions\XPathException;

trait PjaxChecksTrait
{
    private $container = null;
    private $container_xpath = null;

    private function isValidPjaxRequest($request)
    {
        /** @var string|bool $container will be false if container is not valid */
        $container = $this->getContainer($request);

        if ($container !== false) {
            $this->container = $container;
            $this->container_xpath = $this->convertClass($container);

            return true;
        }

        return $container;
    }

    private function getContainer($request)
    {
        /**
         * Get the one from the header, check that it is in the config.
         */
        $container = $this->getContainer($request);

        /**
         * Valid containers from the configuration.
         */
        $valid_containers = config('pjax.valid_containers');

        if (empty($valid_containers) || in_array($container, $valid_containers)) {
            return $container;
        }

        return false;
    }

    private function getContainer($request)
    {
        return $request->header('X-PJAX-CONTAINER');
    }

    /**
     * Send the Pjax Layout Version if it has been set in the config file.
     *
     * @param $reponse
     */
    private function setPjaxLayoutVersion($response)
    {
        $current_version = config('pjax.layout_version');

        if (!empty($current_version)) {
            $response->header('X-PJAX-Version', $current_version);
        }

        return $response;
    }

    /**
     * Convert a class/id to a valid xpath
     *
     * @throws XPathException
     * @param $class_name
     * @return string xpath that can be used to extract content.
     */
    private function convertClass($class_name)
    {
        $first_chr = substr($class_name, 0, 1);
        $find = substr($class_name, 1, strlen($class_name) - 1);

        switch ($first_chr) {
            case '.':
                $xpath = '//*[@class="' . $find . '"]';
                break;

            case '#':
                $xpath = '//*[@id="' . $find . '"]';
                break;

            default:
                throw new XPathException();
                break;
        }

        return $xpath;
    }
}
