@php
/*
View variables:
    - $CUID: string (Client Coded ID)
    - $BEFORE_DEADLINE: string|null (Y-m-d)
===============
*/

$BEFORE_DEADLINE = $BEFORE_DEADLINE ?? null;
@endphp

<x-list-client-past-goals :clientCodedId="$CUID" :beforeDeadline="$BEFORE_DEADLINE" />
