#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
DEST_PLUGIN_DIR=/tmp/easqy/plugin/easqy

cd "$DIR/react/latest_results" && npm run build
cd "$DIR/react/records/adm" && npm run build
cd "$DIR/react/records/pub" && npm run build
cd "$DIR/react/encadrement/adm" && npm run build
cd "$DIR/react/encadrement/pub" && npm run build


rm -rf "$DEST_PLUGIN_DIR"
mkdir -p "$DEST_PLUGIN_DIR"
cp -r "$DIR/dist/plugin/" "$DEST_PLUGIN_DIR"
echo "<?php const EASQY_ENV= EASQY_ENV_PROD; ?>" > "$DEST_PLUGIN_DIR/includes/env.php"
