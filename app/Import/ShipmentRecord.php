<?php

namespace App\Import;

class ShipmentRecord
{
    /**
     * The order no of the shipment.
     *
     * @var int
     */
    private int $orderNo;

    /**
     * The transporter of the shipment.
     *
     * @var string
     */
    private string $transporter;

    /**
     * The reference of the container which contains the goods.
     *
     * @var string
     */
    private string $containerReference;

    /**
     * The purchase order no for this shipment.
     *
     * @var int|null
     */
    private ?int $purchaseOrder;

    /**
     * @param string $orderNo
     * @param string $transporter
     * @param string $containerReference
     * @param int|null $purchaseOrder
     */
    public function __construct(string $orderNo, string $transporter, string $containerReference, ?int $purchaseOrder = null)
    {
        $this->orderNo = $orderNo;
        $this->transporter = $transporter;
        $this->containerReference = $containerReference;
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * Checks if this record is importable.
     *
     * @return bool
     */
    public function isImportable()
    {
        return $this->hasValidContainerRef();
    }

    /**
     * Checks if container reference of this shipment is valid.
     *
     * @return bool
     */
    public function hasValidContainerRef(): bool
    {
        return preg_match('/^[A-Z]{4} \d{6}-\d$/', $this->containerReference);
    }

    /**
     * Returns the tradeflow name for this shipment.
     *
     * @return string
     */
    public function getTradeflowName(): string
    {
        return $this->purchaseOrder ?: sprintf("%s %s", $this->orderNo, $this->transporter);
    }

    /**
     * Returns the order no
     *
     * @return int
     */
    public function getOrderNo(): int
    {
        return $this->orderNo;
    }

    /**
     * Returns the transporter.
     *
     * @return string
     */
    public function getTransporter(): string
    {
        return $this->transporter;
    }

    /**
     * Returns the container reference.
     *
     * @return string
     */
    public function getContainerReference(): string
    {
        return $this->containerReference;
    }

    /**
     * Returns the purchase order no
     *
     * @return int|null
     */
    public function getPurchaseOrder(): ?int
    {
        return $this->purchaseOrder;
    }
}
