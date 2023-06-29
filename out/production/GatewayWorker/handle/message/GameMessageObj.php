<?php

namespace handle\message;

interface GameMessageObj
{
    public function getFrom();

    public function getTo();

    public function getContent();
}