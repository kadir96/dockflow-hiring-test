<?php

namespace App\Import;

class ShipmentRecord
{
    /**
     * The order no of the shipment.
     *
     * @var string
     */
    private string $orderNo;

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
     * @var string|null
     */
    private ?string $purchaseOrder;

    public function __construct(string $orderNo, string $transporter, string $containerReference, ?string $purchaseOrder)
    {
        $this->orderNo = $orderNo;
        $this->transporter = $transporter;
        $this->containerReference = $containerReference;
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * Returns if this record is importable.
     *
     * @return bool
     */
    public function isImportable()
    {
        return $this->hasValidContainerRef();
    }

    /**
     * Returns if container reference of this shipment is valid.
     *
     * @return bool
     */
    public function hasValidContainerRef(): bool
    {
        return preg_match('/[A-Z]{4} \d{6}-\d/', $this->containerReference);
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
     * @return string
     */
    public function getOrderNo(): string
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
     * @return string|null
     */
    public function getContainerReference(): string
    {
        return $this->containerReference;
    }

    /**
     * Returns the purchase order no
     *
     * @return string|null
     */
    public function getPurchaseOrder(): ?string
    {
        return $this->purchaseOrder;
    }
}
