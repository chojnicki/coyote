<?php

namespace Coyote\Services\Elasticsearch\Builders\Job;

use Coyote\Job;
use Coyote\Services\Elasticsearch\Filters\Term;
use Coyote\Services\Elasticsearch\Functions\FieldValueFactor;
use Coyote\Services\Elasticsearch\Functions\Random;
use Coyote\Services\Elasticsearch\QueryBuilder;
use Coyote\Services\Elasticsearch\SimpleQueryString;

class AdBuilder extends SearchBuilder
{
    /**
     * @param string $tags
     */
    public function boostTags(string $tag)
    {
        $this->must(new SimpleQueryString($tag, ['title^2', 'tags.original'], 3));
    }

    /**
     * @return array
     */
    public function build()
    {
        // only premium offers
        $this->must(new Term('is_ads', true));
        $this->must(new Term('model', class_basename(Job::class)));

        $this->score(new FieldValueFactor('score', 'log', 1));
        $this->score(new Random());
        $this->size(0, 4);

        $this->source([
            'id',
            'title',
            'slug',
            'is_remote',
            'remote_range',
            'firm.*',
            'locations',
            'tags',
            'currency_symbol',
            'salary_from',
            'salary_to'
        ]);

        return QueryBuilder::build();
    }
}
