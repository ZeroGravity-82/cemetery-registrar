<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\AbstractApplicationRequestValidator;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateGraveSite\CreateGraveSiteRequest;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcherInterface;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteFetcherInterface;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractGraveSiteRequestValidator extends AbstractApplicationRequestValidator
{
    public function __construct(
        private CemeteryBlockFetcherInterface $cemeteryBlockFetcher,
        private GraveSiteFetcherInterface     $graveSiteFetcher,
        private NaturalPersonFetcherInterface $naturalPersonFetcher,
    ) {
        parent::__construct();
    }

    protected function validateUniquenessConstraints(AbstractApplicationRequest $request): self
    {
        if (
            $request->cemeteryBlockId !== null &&
            $request->rowInBlock      !== null &&
            $this->graveSiteFetcher->doesAlreadyUsedCemeteryBlockIdAndRowInBlockAndPositionInRow(
                $request->id ?? null,
                $request->cemeteryBlockId,
                $request->rowInBlock,
                $request->positionInRow,
            )
        ) {
            $this->note->addError('positionInRow', 'Участок с такими местом в этом ряду уже существует.');
        }

        return $this;
    }

    protected function validateCemeteryBlockId(AbstractApplicationRequest $request): self
    {
        if ($request->cemeteryBlockId === null || empty(\trim($request->cemeteryBlockId))) {
            $this->note->addError('cemeteryBlockId', 'Квартал не указан.');
        }
        if (
            $request->cemeteryBlockId !== null &&
            !$this->cemeteryBlockFetcher->doesExistById($request->cemeteryBlockId)
        ) {
            $this->note->addError('cemeteryBlockId', 'Квартал не найден.');
        }

        return $this;
    }

    protected function validateRowInBlock(AbstractApplicationRequest $request): self
    {
        if ($request->rowInBlock === null) {
            $this->note->addError('rowInBlock', 'Ряд не указан.');
        }
        if ($request->rowInBlock !== null && $request->rowInBlock <= 0) {
            $this->note->addError('rowInBlock', 'Номер ряда должен быть положительным.');
        }

        return $this;
    }

    protected function validatePositionInRow(AbstractApplicationRequest $request): self
    {
        if ($request->positionInRow !== null && $request->positionInRow <= 0) {
            $this->note->addError('positionInRow', 'Номер места в ряду должен быть положительным.');
        }

        return $this;
    }

    protected function validateGeoPosition(AbstractApplicationRequest $request, bool $isRequired = false): self
    {
        switch (true) {
            case
                $isRequired &&
                $request->geoPositionLatitude  === null &&
                $request->geoPositionLongitude === null:
                $this->note->addError('geoPosition', 'Геопозиция не может иметь пустое значение.');
                break;
            case
                $isRequired &&
                ($request->geoPositionLatitude === null || $request->geoPositionLatitude !== null && empty(\trim($request->geoPositionLatitude))):
                $this->note->addError('geoPosition', 'Широта не может иметь пустое значение.');
                break;
            case $request->geoPositionLatitude !== null && !Coordinates::isValidFormat($request->geoPositionLatitude):
                $this->note->addError('geoPosition', 'Неверный формат широты.');
                break;
            case $request->geoPositionLatitude === null &&
                ($request->geoPositionLongitude !== null || $request->geoPositionError !== null):
                $this->note->addError('geoPosition', 'Геопозиция не содержит данных о широте.');
                break;
            case
                $isRequired &&
                ($request->geoPositionLongitude === null || $request->geoPositionLongitude !== null && empty(\trim($request->geoPositionLongitude))):
                $this->note->addError('geoPosition', 'Долгота не может иметь пустое значение.');
                break;
            case $request->geoPositionLongitude !== null && !Coordinates::isValidFormat($request->geoPositionLongitude):
                $this->note->addError('geoPosition', 'Неверный формат долготы.');
                break;
            case $request->geoPositionLongitude === null &&
                ($request->geoPositionLatitude !== null || $request->geoPositionError !== null):
                $this->note->addError('geoPosition', 'Геопозиция не содержит данных о долготе.');
                break;
            case $request->geoPositionError !== null && empty(\trim($request->geoPositionError)):
                $this->note->addError('geoPosition', 'Погрешность не может иметь пустое значение.');
                break;
            case $request->geoPositionError !== null && !Error::isValidFormat($request->geoPositionError):
                $this->note->addError('geoPosition', 'Неверный формат погрешности.');
                break;
        }

        return $this;
    }

    protected function validateSize(AbstractApplicationRequest $request, bool $isRequired = false): self
    {
        if (
            $isRequired &&
            ($request->size === null || $request->size !== null && \trim($request->size) === '')
        ) {
            $this->note->addError('size', 'Размер участка не может иметь пустое значение.');
        } elseif ($request->size !== null && (int) ((float) $request->size * 10) === 0) {
            $this->note->addError('size', 'Размер участка не может иметь нулевое значение.');
        } elseif ($request->size !== null && !GraveSiteSize::isValidFormat($request->size)) {
            $this->note->addError('size', 'Неверный формат размера участка.');
        }

        return $this;
    }

    protected function validatePersonInChargeId(AbstractApplicationRequest $request): self
    {
        if (
            $request->personInChargeId !== null &&
            !$this->naturalPersonFetcher->doesExistById($request->personInChargeId)
        ) {
            $this->note->addError('personInChargeId', 'Физлицо не найдено.');
        }

        return $this;
    }
}
