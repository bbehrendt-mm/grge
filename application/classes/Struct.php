<?php

abstract class Struct {
    public function __get( $property ) {
        throw new RuntimeException( "StructGuard violation: Read access to invalid property $property." );
    }

    public function __set( $property, $value ) {
        throw new RuntimeException( "StructGuard violation: Write access to invalid property $property." );
    }

    public function __isset( $property ) {
        throw new RuntimeException( "StructGuard violation: ISSET check on invalid property $property." );
    }
}