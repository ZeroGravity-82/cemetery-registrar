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
    protected function validateId(ApplicationRequest $request): self
    {
        if (
            $this->hasProperty($request, 'id') &&
            ($request->id === null || empty(\trim($request->id)))
        ) {
            $this->note->addError('id', 'Идентификатор участка не задан.');
        }

        return $this;
    }

    protected function validateCemeteryBlockId(ApplicationRequest $request): self
    {
        if (
            $this->hasProperty($request, 'cemeteryBlockId') &&
            ($request->cemeteryBlockId === null || empty(\trim($request->cemeteryBlockId)))
        ) {
            $this->note->addError('cemeteryBlockId', 'Квартал не выбран.');
        }

        return $this;
    }

    protected function validateRowInBlock(ApplicationRequest $request): self
    {
        if (
            $this->hasProperty($request, 'rowInBlock') &&
            $request->rowInBlock === null
        ) {
            $this->note->addError('id', 'Ряд не задан.');
        }
        if (
            $this->hasProperty($request, 'rowInBlock') &&
            $request->rowInBlock <= 0
        ) {
            $this->note->addError('rowInBlock', 'Номер ряда должен быть положительным.');
        }

        return $this;
    }

    protected function validatePositionInRow(ApplicationRequest $request): self
    {
        if (
            $this->hasProperty($request, 'positionInRow') &&
            $request->positionInRow !== null              &&
            $request->positionInRow <= 0
        ) {
            $this->note->addError('positionInRow', 'Номер места в ряду должен быть положительным.');
        }

        return $this;
    }

    protected function validateGeoPositionLatitude(ApplicationRequest $request): self
    {
        if (
            $this->hasProperty($request, 'geoPositionLatitude') &&
            $request->geoPositionLatitude !== null              &&
            empty(\trim($request->geoPositionLatitude))
        ) {
            $this->note->addError('geoPositionLatitude', 'Широта не может иметь пустое значение.');
        }
        if (
            $this->hasProperty($request, 'geoPositionLatitude') &&
            $request->geoPositionLatitude !== null              &&
            !Coordinates::isValidFormat($request->geoPositionLatitude)
        ) {
            $this->note->addError('geoPositionLatitude', 'Неверный формат широты.');
        }

        return $this;
    }

    protected function validateGeoPositionLongitude(ApplicationRequest $request): self
    {
        if (
            $this->hasProperty($request, 'geoPositionLongitude') &&
            $request->geoPositionLongitude !== null              &&
            empty(\trim($request->geoPositionLongitude))
        ) {
            $this->note->addError('geoPositionLongitude', 'Долгота не может иметь пустое значение.');
        }
        if (
            $this->hasProperty($request, 'geoPositionLongitude') &&
            $request->geoPositionLongitude !== null              &&
            !Coordinates::isValidFormat($request->geoPositionLongitude)
        ) {
            $this->note->addError('geoPositionLongitude', 'Неверный формат долготы.');
        }

        return $this;
    }

    protected function validateGeoPositionError(ApplicationRequest $request): self
    {
        if (
            $this->hasProperty($request, 'geoPositionError') &&
            $request->geoPositionError !== null              &&
            empty(\trim($request->geoPositionError))
        ) {
            $this->note->addError('geoPositionError', 'Погрешность не может иметь пустое значение.');
        }
        if (
            $this->hasProperty($request, 'geoPositionError') &&
            $request->geoPositionError !== null              &&
            !Error::isValidFormat($request->geoPositionError)
        ) {
            $this->note->addError('geoPositionError', 'Неверный формат погрешности.');
        }

        return $this;
    }

    protected function validateSize(ApplicationRequest $request): self
    {
        if (
            $this->hasProperty($request, 'size') &&
            $request->size !== null              &&
            empty(\trim($request->size))
        ) {
            $this->note->addError('size', 'Размер участка не может иметь пустое значение.');
        }
        if (
            $this->hasProperty($request, 'size') &&
            $request->size !== null              &&
            !GraveSiteSize::isValidFormat($request->size)
        ) {
            $this->note->addError('size', 'Неверный формат размера участка.');
        }

        return $this;
    }
}
