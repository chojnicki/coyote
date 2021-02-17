<?php

namespace Coyote\Repositories\Contracts;

use Coyote\Tag;

interface TagRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     * @return \Coyote\Tag[]
     */
    public function lookupName($name);

    public function exists(string $name): bool;

    /**
     * @param string[] $tags
     * @return int[] Ids of tags
     */
    public function multiInsert(array $tags);

    /**
     * @param array $tags
     * @return Tag[]
     */
    public function categorizedTags(array $tags);
}
