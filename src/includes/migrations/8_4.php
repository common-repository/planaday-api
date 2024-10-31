<?php

function update8_4()
{
    pad_update_option('toonprijsdetailpagina', '1');
	update_option( 'planaday-api-version', '8.4' );
}