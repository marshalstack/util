<?php

declare(strict_types=1);

namespace Marshal\Util\FileSystem\Local;

use League\Flysystem\Filesystem;

class FileManager
{
    public function __construct(private Filesystem $filesystem)
    {
    }

    public function getTemplateContents(string $templateFileName): string
    {
        $template = $this->filesystem->read($templateFileName);
        if (! $template) {
            throw new \RuntimeException(\sprintf(
                "Template file %s not found",
                $templateFileName
            ));
        }

        return $this->parseResource($templateFileName, $template);
    }

    private function parseResource(string $resourceName, string $contents): string
    {
        if (
            false !== \mb_strpos($resourceName, '.json')
            || false !== \mb_strpos($resourceName, '.html')
            || false !== \mb_strpos($resourceName, '.twig')
        ) {
            return $contents;
        }

        throw new \RuntimeException(\sprintf(
            "Could not parse resource %s",
            $resourceName
        ));
    }
}
