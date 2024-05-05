<?php

namespace Theme\Modules\Views;

use Saurus\App\Interfaces\IViewModule;
use Theme\PostTypes\ContactLocation;
use Theme\PostTypes\Meteostation;
use Illuminate\Support\Collection;
use Theme\Taxonomies\ExampleCategory;

class ArchiveExample implements IViewModule
{
    private iterable $something;

    private function getSomething() {
        return ['foo' => 'bar'];
    }

    public function provideData() : array
    {
        $toReturn = [];

        $toReturn[] = [
            'smthing' => $this->getSomething()
        ];

        return $toReturn;
    }
}