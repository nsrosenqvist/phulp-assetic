<?php namespace NSRosenqvist\Phulp;

use Assetic\Asset\StringAsset;
use Assetic\Asset\AssetCollection;
use Phulp\DistFile;
use Phulp\Source;

class Assetic implements \Phulp\PipeInterface
{
    private $filters;
    private $filename;

    public function __construct($filters, $filename = null)
    {
        $this->filename = $filename;
        $filters = (! is_array($filters)) ? [$filters] : $filters;

        // Remove all elements that aren't implementations of FilterInterface
        foreach ($filters as $key => $filter) {
            if (! $filter instanceof \Assetic\Filter\FilterInterface) {
                unset($filters[$key]);
            }
        }

        $this->filters = $filters ?: null;
    }

    public function execute(Source $src)
    {
        // Skip if no filters are set
        if (! $this->filters) {
            return;
        }

        // Create an array of each file as an Assetic Asset
        $assets = [];
        $files = $src->getDistFiles();

        foreach ($files as $key => $file) {
            $assets[$key] = new StringAsset($file->getContent());
        }

        // If a filename is set we concat the files. This allows us to process the list
        // of files more effectively in a batch process
        if (is_string($this->filename)) {
            foreach ($assets as $key => $asset) {
                $src->removeDistFile($key);
            }

            $collection = new AssetCollection($assets, $this->filters);
            $src->addDistFile(new DistFile($collection->dump(), $this->filename));
        }
        // Process each file one by one
        else {
            foreach ($assets as $key => $asset) {
                $collection = new AssetCollection([$asset], $this->filters);
                $files[$key]->setContent($collection->dump());
            }
        }
    }
}
