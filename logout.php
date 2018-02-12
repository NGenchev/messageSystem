<?php

if(session_start() && session_destroy())
	die(header('Location: index.php'));