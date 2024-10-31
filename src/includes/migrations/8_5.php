<?php

function update8_5()
{
    pad_update_option('vraagcostcentercodemanatory', '0');
	update_option( 'planaday-api-version', '8.5' );
}