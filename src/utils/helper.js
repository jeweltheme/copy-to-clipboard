import { registerBlockType } from "@wordpress/blocks";

export const mapfyRegisterBlockType = (metadata, newObj) => {
    const metaData = {
        title: metadata.title,
        description: metadata.description,
        category: metadata.category,
        supports: metadata.supports,
    };

    return registerBlockType(metadata.name, {
        ...metaData,
        ...newObj,
    });
};
