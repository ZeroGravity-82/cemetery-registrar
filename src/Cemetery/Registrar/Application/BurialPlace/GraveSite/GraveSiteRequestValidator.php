<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class GraveSiteRequestValidator extends ApplicationRequestValidator
{
    public function __construct(
        private readonly CemeteryBlockFetcher $cemeteryBlockFetcher,
        private readonly GraveSiteFetcher     $graveSiteFetcher,
    ) {
        parent::__construct();
    }

    protected function validateUniquenessConstraints(ApplicationRequest $request): self
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

    protected function validateCemeteryBlockId(ApplicationRequest $request): self
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

    protected function validateRowInBlock(ApplicationRequest $request): self
    {
        if ($request->rowInBlock === null) {
            $this->note->addError('rowInBlock', 'Ряд не указан.');
        }
        if ($request->rowInBlock !== null && $request->rowInBlock <= 0) {
            $this->note->addError('rowInBlock', 'Номер ряда должен быть положительным.');
        }

        return $this;
    }

    protected function validatePositionInRow(ApplicationRequest $request): self
    {
        if ($request->positionInRow !== null && $request->positionInRow <= 0) {
            $this->note->addError('positionInRow', 'Номер места в ряду должен быть положительным.');
        }

        return $this;
    }

    protected function validateGeoPosition(ApplicationRequest $request, bool $isRequired = false): self
    {
        switch (true) {
            case $request->geoPositionLatitude === null && $isRequired ||
                 $request->geoPositionLatitude !== null && empty(\trim($request->geoPositionLatitude)):
                $this->note->addError('geoPosition', 'Широта не может иметь пустое значение.');
                break;
            case $request->geoPositionLatitude !== null && !Coordinates::isValidFormat($request->geoPositionLatitude):
                $this->note->addError('geoPosition', 'Неверный формат широты.');
                break;
            case $request->geoPositionLatitude === null &&
                ($request->geoPositionLongitude !== null || $request->geoPositionError !== null):
                $this->note->addError('geoPosition', 'Геопозиция не содержит данных о широте.');
                break;
            case $request->geoPositionLongitude === null && $isRequired ||
                 $request->geoPositionLongitude !== null && empty(\trim($request->geoPositionLongitude)):
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

    protected function validateSize(ApplicationRequest $request, bool $isRequired = false): self
    {
        if ($request->size === null && $isRequired ||
            $request->size !== null && \trim($request->size) === ''
        ) {
            $this->note->addError('size', 'Размер участка не может иметь пустое значение.');
        } elseif ($request->size !== null && (int) ((float) $request->size * 10) === 0) {
            $this->note->addError('size', 'Размер участка не может иметь нулевое значение.');
        } elseif ($request->size !== null && !GraveSiteSize::isValidFormat($request->size)) {
            $this->note->addError('size', 'Неверный формат размера участка.');
        }

        return $this;
    }
}
