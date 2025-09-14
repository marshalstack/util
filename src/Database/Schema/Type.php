<?php

declare(strict_types= 1);

namespace Marshal\Util\Database\Schema;

final class Type
{
    /**
     * @return array<Type>
     */
    private array $parents = [];

    /**
     * @var array<Property>
     */
    private array $properties = [];

    public function __construct(
        private string $identifier,
        private string $database,
        private string $table,
        private array $config
    ) {
        // foreach ($config['properties'])
    }

    public function addParent(Type $type): static
    {
        $this->parents[$type->getName()] = $type;
        foreach ($type->getProperties() as $property) {
            if ($this->hasProperty($property->getName())) {
                continue;
            }

            $this->properties[$property->getName()] = $property;
        }

        return $this;
    }

    public function getAutoIncrement(): Property
    {
        foreach ($this->properties as $property) {
            if ($property->isAutoIncrement()) {
                return $property;
            }
        }

        throw new \InvalidArgumentException("no autoincrement property");
    }

    public function getCollectionTemplate(): string
    {
        return $this->config['templates']['collection'];
    }

    public function getContentTemplate(): string
    {
        return $this->config['templates']['content'];
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getDescription(): string
    {
        return $this->config["description"];
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->config["name"];
    }

    public function getParents(): array
    {
        return $this->parents;
    }

    /**
     * @return array<Property>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $name): Property
    {
        if (! $this->hasProperty($name)) {
            throw new \InvalidArgumentException(\sprintf("Property %s does not exist", $name));
        }

        return $this->properties[$name];
    }

    public function getPropertyByIdentifier(string $identifier): Property
    {
        foreach ($this->getProperties() as $property) {
            if ($property->getIdentifier() === $identifier) {
                return $property;
            }
        }

        throw new \InvalidArgumentException(
            \sprintf("Property %s does not exist in %s", $identifier, $this->getName())
        );
    }

    public function getRoutePrefix(): string
    {
        return $this->config['routing']['route_prefix'] ?? '';
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getValidators(): array
    {
        return $this->config['validators'] ?? [];
    }

    public function hasCollectionTemplate(): bool
    {
        return isset($this->config['templates']['collection']);
    }

    public function hasContentTemplate(): bool
    {
        return isset($this->config['templates']['content']);
    }

    public function hasProperty(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function hasPropertyIdentifier(string $identifier): bool
    {
        foreach ($this->getProperties() as $property) {
            if ($identifier === $property->getIdentifier()) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function hasRoutePrefix(): bool
    {
        return isset($this->config['routing']['route_prefix']);
    }

    public function removeProperty(string $identifier): static
    {
        foreach ($this->getProperties() as $name => $property) {
            if ($identifier !== $property->getIdentifier()) {
                continue;
            }

            unset($this->properties[$name]);
        }
        return $this;
    }

    public function setProperty(Property $property): static
    {
        $this->properties[$property->getName()] = $property;
        return $this;
    }

    public function toArray(): array
    {
        $properties = [];
        foreach ($this->getProperties() as $property) {
            $properties[$property->getName()] = $property->toArray();
        }

        return [
            "description"=> $this->getDescription(),
            "properties" => $properties,
            "validators" => $this->getValidators(),
        ];
    }
}
