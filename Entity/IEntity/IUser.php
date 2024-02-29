<?php

namespace Entity\IEntity;

interface IUser {
    public function getUsername(): string;
    public function getPassword(): string;
    public function getName(): string;
    public function setUsername(string $username): void;
    public function setPassword(string $password): void;
    public function setName(string $name): void;
}