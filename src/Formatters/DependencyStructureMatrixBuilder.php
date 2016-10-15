<?php

declare(strict_types=1);

namespace Mihaeu\PhpDependencies\Formatters;

use Mihaeu\PhpDependencies\Dependencies\Dependency;
use Mihaeu\PhpDependencies\Dependencies\DependencySet;
use Mihaeu\PhpDependencies\Dependencies\DependencyMap;

class DependencyStructureMatrixBuilder
{
    public function buildMatrix(DependencyMap $dependencies, \Closure $mappers) : array
    {
        $emptyDsm = $this->createEmptyDsm($dependencies->mapAllDependencies($mappers));
        return $dependencies->reduce($emptyDsm, function (array $dsm, Dependency $from, Dependency $to) use ($mappers) : array {
            $from = $mappers($from)->toString();
            $to = $mappers($to)->toString();
            if ($from === $to
                || strlen($from) === 0
                || strlen($to) === 0) {
                return $dsm;
            }
            $dsm[$from][$to] += 1;
            return $dsm;
        });
    }

    /**
     * @param $dependencies
     *
     * @return array
     */
    private function createEmptyDsm(DependencySet $dependencies)
    {
        return $dependencies->reduce([], function (array $combined, Dependency $dependency) use ($dependencies) {
            $combined[$dependency->toString()] = array_combine(
                array_values($dependencies->toArray()),     // keys:    dependency name
                array_pad([], $dependencies->count(), 0)    // values:  [0, 0, 0, ... , 0]
            );
            return $combined;
        });
    }
}
