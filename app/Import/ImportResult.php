<?php

namespace App\Import;

use App\Tradeflow;

class ImportResult
{
    /**
     * The imported tradeflows.
     *
     * @var Tradeflow[]
     */
    private array $tradeflows;

    /**
     * Invalid container references.
     *
     * @var string[]
     */
    private array $invalidContainerReferences;

    /**
     * Tradeflows with no container associated.
     *
     * @var Tradeflow[]
     */
    private array $tradeflowsWithoutContainers;

    public function __construct(array $tradeflows, array $invalidContainerReferences, array $tradeflowsWithoutContainers)
    {
        $this->tradeflows = $tradeflows;
        $this->invalidContainerReferences = $invalidContainerReferences;
        $this->tradeflowsWithoutContainers = $tradeflowsWithoutContainers;
    }

    /**
     * Returns the imported tradeflows.
     *
     * @return Tradeflow[]|array
     */
    public function getTradeflows(): array
    {
        return $this->tradeflows;
    }

    /**
     * Returns the invalid container references.
     *
     * @return array|string[]
     */
    public function getInvalidContainerReferences(): array
    {
        return $this->invalidContainerReferences;
    }

    /**
     * Return tradeflows without any container.
     *
     * @return Tradeflow[]|array
     */
    public function getTradeflowsWithoutContainers(): array
    {
        return $this->tradeflowsWithoutContainers;
    }

}
