<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\MongoDB\Aggregation\Stage;

use Doctrine\MongoDB\Aggregation\Builder;
use Doctrine\MongoDB\Aggregation\Stage;

/**
 * Fluent interface for adding a $facet stage to an aggregation pipeline.
 *
 * @author alcaeus <alcaeus@alcaeus.org>
 * @since 1.5
 */
class Facet extends Stage
{
    /**
     * @var Builder[]
     */
    private $pipelines = [];

    /**
     * @var string
     */
    private $field;

    /**
     * {@inheritdoc}
     */
    public function getExpression()
    {
        return [
            '$facet' => array_map(function (Builder $builder) { return $builder->getPipeline(); }, $this->pipelines),
        ];
    }

    /**
     * Set the current field for building the pipeline stage.
     *
     * @param string $field
     *
     * @return $this
     */
    public function field($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Use the given pipeline for the current field.
     *
     * @param Builder|Stage $builder
     * @return $this
     */
    public function pipeline($builder)
    {
        if (!$this->field) {
            throw new \LogicException(__METHOD__ . ' requires you set a current field using field().');
        }

        if ($builder instanceof Stage) {
            $builder = $builder->builder;
        }

        if (!$builder instanceof Builder) {
            throw new \InvalidArgumentException(__METHOD__ . ' expects either an aggregation builder or an aggregation stage.');
        }

        $this->pipelines[$this->field] = $builder;
        return $this;
    }
}