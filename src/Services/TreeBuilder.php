<?php

namespace Molitor\Product\Services;

abstract class TreeBuilder
{
    private array $rootIds = [];
    private array $children = [];
    private array $items = [];

    public function idExists(int $id): bool
    {
        return array_key_exists($id, $this->items);
    }

    public function hasParent(int $id): bool
    {
        return in_array($id, $this->rootIds);
    }

    public function add(int $id, int $parentId, array $data): bool
    {
        if($this->idExists($id)) {
            return false;
        }

        $this->items[$id] = [
            'parent_id' => $parentId,
            'data' => $data,
        ];

        if($parentId === 0) {
            $this->rootIds[] = $id;
        }
        else {
            if(array_key_exists($parentId, $this->children)) {
                $this->children[$parentId][] = $id;
            }
            else {
                $this->children[$parentId] = [$id];
            }
        }
    }

    public function getChildrenIds(int $id): array
    {
        if($id === 0) {
            return $this->rootIds;
        }
        if(array_key_exists($id, $this->children)) {
            return $this->children[$id];
        }
        return [];
    }

    public function getItem(int $id): array
    {
        return $this->items[$id];
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getPath(int $id): array
    {
        if(!$this->idExists($id)) {
            return [];
        }
    }
}
