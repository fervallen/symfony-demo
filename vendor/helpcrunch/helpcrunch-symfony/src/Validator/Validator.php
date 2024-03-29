<?php

namespace Helpcrunch\Validator;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Helpcrunch\Validator\Constraints\UniqueValue;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Doctrine\Common\Annotations\AnnotationException;

final class Validator
{
    use HelpcrunchServicesTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $errors = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param HelpcrunchEntity $entity
     * @param array $data
     * @return bool|HelpcrunchEntity
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function isValid(HelpcrunchEntity $entity, array $data)
    {
        $collector = new ValidationRulesCollector();
        $collector->collectRules($entity);

        $data = $this->validateRelations($entity, $collector->getEntitiesRelations(), $data);

        $this->validateData($entity, $collector->getValidationRules(), $data);
        if (!count($this->errors)) {
            return $this->createEntity($entity, $data);
        }

        return false;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function validateRelations(HelpcrunchEntity $entity, array $relations, array $data): array
    {
        foreach ($relations as $key => $relation) {
            if ($entity->id && empty($data[$key])) {
                continue;
            }
            if (empty($relation['targetEntity'])) {
                continue;
            }

            $targetEntity = $this->getRelationIfExists(
                $relation['targetEntity'],
                $relation['nullable'] ?? true,
                $key,
                $data[$key] ?? null
            );
            if ($targetEntity) {
                $data[$key] = $targetEntity;
            }
        }

        return $data;
    }

    private function getRelationIfExists(
        string $targetEntityClass,
        bool $nullAble,
        string $targetEntityField,
        int $targetEntityId = null
    ) {
        $repository = $this->getEntityManager()->getRepository($targetEntityClass);

        if ($targetEntityId) {
            $targetEntity = $repository->find($targetEntityId);

            if ($targetEntity) {
                return $targetEntity;
            } elseif (!$targetEntity && !$nullAble) {
                $this->errors[$targetEntityField] = $targetEntityClass . ' does not exist';
            }
        } else {
            if (!$nullAble) {
                $this->errors[$targetEntityField] = $targetEntityClass . ' can not be null';
            }
        }

        return false;
    }

    private function validateData(HelpcrunchEntity $entity, array $rules, array $data): void
    {
        $validation = Validation::createValidator();
        foreach ($rules as $field => $validationRules) {
            if ($entity->id && empty($data[$field])) {
                continue;
            }
            if (empty($rules[$field])) {
                continue;
            }

            $validationRules = $this->checkUniqueValueOnUpdate($entity, $validationRules);

            $violation = $validation->validate($data[$field] ?? null, $validationRules);
            $this->collectErrors($field, $violation);
        }
    }

    private function checkUniqueValueOnUpdate(HelpcrunchEntity $entity, array $constraints): array
    {
        foreach ($constraints as $key => $constraint) {
            if ($entity->id && ($constraint instanceof UniqueValue)) {
                unset($constraints[$key]);
            }
        }

        return $constraints;
    }

    private function collectErrors($name, ConstraintViolationListInterface $violation): void
    {
        if ($violation->count()) {
            $this->errors[$name] = $violation[0]->getMessage();
        }
    }

    private function createEntity(HelpcrunchEntity $entity, array $data): HelpcrunchEntity
    {
        foreach ($data as $key => $value) {
            if (property_exists($entity, $key)) {
                $entity->$key = $value;
            }
        }

        return $entity;
    }
}
