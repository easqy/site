<?php
const EASQY_ENV_DEV= 0;
const EASQY_ENV_PROD= 1;
require __DIR__ . '/env.php';

class Easqy_Settings {

	const ENV_DEV = EASQY_ENV_DEV;
	const ENV_PROD= EASQY_ENV_PROD;
	const Environnement = EASQY_ENV;
	public static function is_prod() : bool { return self::Environnement === self::ENV_PROD; }
	public static function is_dev() : bool { return self::Environnement === self::ENV_DEV; }
}

function easqy_env_is_prod() : bool { return Easqy_Settings::is_prod(); }
function easqy_env_is_dev() : bool { return Easqy_Settings::is_dev(); }
