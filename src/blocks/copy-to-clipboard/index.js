import { __ } from "@wordpress/i18n";
// import attributes from './attributes';
import edit from "./edit";
// import save from "./save";
// import icon from "./components/icon";

import metadata from "./block.json";
import { mapfyRegisterBlockType } from "../../utils/helper";

mapfyRegisterBlockType(metadata, {
    icon: "dashicons dashicons-align-pull-right",
    edit: edit,
    save: () => null,
});
