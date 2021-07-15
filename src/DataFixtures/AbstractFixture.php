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
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractFixture extends Fixture
{
    /** @var string */
    protected $class;

    /** @var string */
    protected $fixtureName;

    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var ValidatorInterface */
    protected $validator;

    public function __construct(PropertyAccessorInterface $propertyAccessor, ValidatorInterface $validator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->validator = $validator;
    }

    /**
     * Load a fixture.
     *
     * @param string|null $fixtureName The fixture name
     *
     * @return mixed
     *
     * @throws \ReflectionException
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

        foreach ($fixtures as $index => $data) {
            $entity = $this->buildEntity($data, $index);

            if (null !== $entity) {
                $errors = $this->validator->validate($entity);
                if (\count($errors) > 0) {
                    throw new \InvalidArgumentException(Yaml::dump($data).PHP_EOL.(string) $errors);
                }

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
        $metadata = $this->getMetadata($entity);
        foreach ($data as $propertyPath => $value) {
            if (0 === strpos($propertyPath, '@')) {
                continue;
            }

            // Convert references to actual entities for associations.
            if ($metadata->hasAssociation($propertyPath)) {
                if ($metadata->isCollectionValuedAssociation($propertyPath) && \is_array($value)) {
                    $value = array_map([$this, 'getEntityReference'], $value);
                } else {
                    $value = $this->getEntityReference($value);
                }
            } elseif (isset($metadata->fieldMappings[$propertyPath]['type'])) {
                $value = $this->convert($value, $metadata->fieldMappings[$propertyPath]['type']);
            }

            try {
                $this->propertyAccessor->setValue($entity, $propertyPath, $value);
            } catch (\Exception $exception) {
                throw new \RuntimeException(sprintf('Cannot set property %s.%s on entity %s', \get_class($entity), $propertyPath, $entity));
            }
        }

        return $entity;
    }

    protected function getMetadata($entity)
    {
        return $this->referenceRepository->getManager()->getClassMetadata(\get_class($entity));
    }

    protected function getEntityReference($reference)
    {
        if (0 === strpos($reference, '@')) {
            return $this->getReference(substr($reference, 1));
        }
        throw new \RuntimeException(sprintf('Invalid reference: %s', $reference));
    }

    /**
     * Convert a scalar value to the requested type.
     *
     * @param $value
     * @param $type
     *
     * @return mixed
     */
    protected function convert($value, $type)
    {
        switch ($type) {
            case 'datetime':
                return new \DateTime($value);
        }

        return $value;
    }
}
