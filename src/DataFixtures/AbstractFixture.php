<?php

/*
 * This file is part of aakb/kontrolgruppen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractFixture extends Fixture
{
    /** @var string */
    protected $class;

    /** @var string */
    protected $fixtureName;

    /** @var PropertyAccessor */
    protected $accessor;

    /**
     * @param string|null $fixtureName
     *
     * @return mixed
     */
    protected function loadFixture(string $fixtureName = null)
    {
        if (null === $fixtureName) {
            if (null !== $this->fixtureName) {
                $fixtureName = $this->fixtureName;
            } else {
                $name = (new \ReflectionClass($this->class))->getShortName();
                $fixtureName = $name.'.yaml';
            }
        }
        $basepath = __DIR__.'/Data';
        $content = file_get_contents($basepath.'/'.$fixtureName);

        return Yaml::parse($content);
    }

    public function load(ObjectManager $manager)
    {
        if (null === $this->class) {
            throw new \RuntimeException('No class defined in '.static::class);
        }

        $fixtures = $this->loadFixture();
        $this->accessor = new PropertyAccessor();

        foreach ($fixtures as $index => $data) {
            $entity = $this->buildEntity($data, $index);
            if (null !== $entity) {
                $manager->persist($entity);
            }

            if (isset($data['@id'])) {
                $this->addReference($data['@id'], $entity);
            }
        }
        $manager->flush();
    }

    protected function buildEntity(array $data, $index)
    {
        $entity = new $this->class();
        foreach ($data as $propertyPath => $value) {
            if (0 === strpos($propertyPath, '@')) {
                continue;
            }
            if (0 === strpos($value, '@')) {
                $value = $this->getReference(substr($value, 1));
            }
            if ($this->accessor->isWritable($entity, $propertyPath)) {
                $this->accessor->setValue($entity, $propertyPath, $value);
            }
        }

        return $entity;
    }
}
