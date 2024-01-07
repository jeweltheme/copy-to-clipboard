import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";

/**
 *
 * @param props
 */
export default function Edit(props) {
    return <div {...useBlockProps()}>This is Edit page</div>;
}
