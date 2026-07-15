<?php

declare(strict_types=1);

namespace Molitor\Product\Services;

use Illuminate\Support\Facades\Gate;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Molitor\Setting\Enums\SettingFieldType;
use Molitor\Setting\Services\SettingForm;

class ProductSettingForm extends SettingForm
{
    public function __construct(
        protected CurrencyRepositoryInterface $currencyRepository
    ) {}

    public function getSlug(): string
    {
        return 'product';
    }

    public function getLabel(): string
    {
        return 'Termékek';
    }

    public function getFields(): array
    {
        return [
            'currency' => [
                'label' => 'Deviza',
                'type' => SettingFieldType::Select,
                'options' => $this->getCurrencyOptions(),
                'default' => $this->currencyRepository->getDefaultId(),
            ],
        ];
    }

    public function getValues(): array
    {
        return [
            'currency' => $this->currencyRepository->getDefaultId(),
        ];
    }

    public function afterSave(array $values): void
    {
        $currencyId = $values['currency'] ?? null;
        if ($currencyId === null) {
            return;
        }

        $currency = $this->currencyRepository->getById((int) $currencyId);
        if ($currency !== null && $currency->id !== $this->currencyRepository->getDefaultId()) {
            $this->currencyRepository->setDefault($currency);
        }
    }

    public function canAccess(): bool
    {
        return parent::canAccess() && Gate::allows('acl', 'product');
    }

    private function getCurrencyOptions(): array
    {
        $options = [];
        foreach ($this->currencyRepository->getEnabledCurrencies() as $currency) {
            $options[] = ['value' => $currency->id, 'label' => $currency->code];
        }

        return $options;
    }
}
