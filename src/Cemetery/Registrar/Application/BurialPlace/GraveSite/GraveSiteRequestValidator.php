<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class GraveSiteRequestValidator extends ApplicationRequestValidator
{
    protected function validateCemeteryBlockId(ApplicationRequest $request): self
    {
        if ($request->cemeteryBlockId === null || empty(\trim($request->cemeteryBlockId))) {
            $this->note->addError('cemeteryBlockId', 'Квартал не указан.');
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

    protected function validateGeoPositionLatitude(ApplicationRequest $request): self
    {
        if ($request->geoPositionLatitude !== null && empty(\trim($request->geoPositionLatitude))) {
            $this->note->addError('geoPosition', 'Широта не может иметь пустое значение.');
        }
        if ($request->geoPositionLatitude !== null && !Coordinates::isValidFormat($request->geoPositionLatitude)) {
            $this->note->addError('geoPosition', 'Неверный формат широты.');
        }
        if ($request->geoPositionLatitude === null &&
            ($request->geoPositionLongitude !== null || $request->geoPositionError !== null)) {
            $this->note->addError('geoPosition', 'Геопозиция не содержит данных о широте.');
        }

        return $this;
    }

    protected function validateGeoPositionLongitude(ApplicationRequest $request): self
    {
        if ($request->geoPositionLongitude !== null && empty(\trim($request->geoPositionLongitude))) {
            $this->note->addError('geoPosition', 'Долгота не может иметь пустое значение.');
        }
        if ($request->geoPositionLongitude !== null && !Coordinates::isValidFormat($request->geoPositionLongitude)) {
            $this->note->addError('geoPosition', 'Неверный формат долготы.');
        }
        if ($request->geoPositionLongitude === null &&
            ($request->geoPositionLatitude !== null || $request->geoPositionError !== null)) {
            $this->note->addError('geoPosition', 'Геопозиция не содержит данных о долготе.');
        }

        return $this;
    }

    protected function validateGeoPositionError(ApplicationRequest $request): self
    {
        if ($request->geoPositionError !== null && empty(\trim($request->geoPositionError))) {
            $this->note->addError('geoPosition', 'Погрешность не может иметь пустое значение.');
        }
        if ($request->geoPositionError !== null && !Error::isValidFormat($request->geoPositionError)) {
            $this->note->addError('geoPosition', 'Неверный формат погрешности.');
        }

        return $this;
    }

    protected function validateSize(ApplicationRequest $request): self
    {
        if ($request->size !== null && empty(\trim($request->size))) {
            $this->note->addError('size', 'Размер участка не может иметь пустое значение.');
        }
        if ($request->size !== null && !GraveSiteSize::isValidFormat($request->size)) {
            $this->note->addError('size', 'Неверный формат размера участка.');
        }

        return $this;
    }
}
