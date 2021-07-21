<?php

namespace EBilling\Response;

final class DniResponse {
    private $firstName;

    private $lastNameOne;

    private $lastNameTwo;

    public function __construct($firstName, $lastNameOne, $lastNameTwo)
    {
        $this->firstName = $firstName;
        $this->lastNameOne = $lastNameOne;
        $this->lastNameTwo = $lastNameTwo;
    }

    public function toArray()
    {
        return [
            'nombres' => $this->firstName,
            'apellido_paterno' => $this->lastNameOne,
            'apellido_materno' => $this->lastNameTwo,
        ];
    }
}
