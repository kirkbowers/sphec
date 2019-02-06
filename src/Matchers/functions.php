<?php

function is_or_evaluates_to_falsey($value) {
  return !$value || (is_callable($value) && !$value());
}

function succeeds_matching($value) {
  return !is_or_evaluates_to_falsey($value);
}