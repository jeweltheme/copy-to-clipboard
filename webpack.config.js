const defaultConfig = require("@wordpress/scripts/config/webpack.config.js");
const path = require("path");
const glob = require("glob");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const RemovePlugin = require("remove-files-webpack-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const UnminifiedWebpackPlugin = require("unminified-webpack-plugin");

const isProduction = "production" === process.env.NODE_ENV;
const mode = isProduction ? "production" : "development";

defaultConfig.output = {
    ...defaultConfig.output,
    path: path.resolve(process.cwd(), "assets"),
};

// "assets/js/darken": glob
//     .sync("./dev/admin/darken.js")

defaultConfig.entry = {
    "js/copy-to-clipboard-admin": "./dev/js/copy-to-clipboard-admin.js",
    // "js/darken": "./dev/darken",
    // "js/darken-frontend": "./dev/darken",
};

/**
 *
 */
function getEntries() {
    return (
        glob
            .sync("./src/blocks/**/**/*style.scss")
            // .map((path) => './' + path.replace('./', ''))
            .map((path) => {
                let name = path.substring(6, path.length - 18);

                name = "public/css/" + name;

                return { name, path };
            })
            .reduce((memo, file) => {
                memo[file.name] = file.path;
                return memo;
            }, {})
    );
}

/**
 * Get all entries JS.
 */
function getEntriesOfJs() {
    return glob
        .sync("./src/blocks/**/*frontend.js")
        .map((path) => "./" + path.replace("./", ""))
        .map((path) => {
            let name = path.substring(6, path.length - 12);
            name = "public/js/" + name;
            return { name, path };
        })
        .reduce((memo, file) => {
            memo[file.name] = file.path;
            return memo;
        }, {});
}

const assetsConfig = {
    mode,
    entry: {
        // All blocks public styles in separate file
        ...getEntries(),
        ...getEntriesOfJs(),
        // // Plugin Deactivation Survey
        // "admin/css/mapfy-survey": glob
        //     .sync("./src/scss/survey.scss")
        //     .map((path) => "./" + path.replace("./", "")),

        // Frontend css
        // "public/css/copy-to-clipboard-frontend": [
        //     ...glob
        //         .sync("./dev/scss/copy-to-clipboard-frontent.scss")
        //         .map((path) => "./" + path.replace("./", "")),
        //     // All blocks style css
        //     ...glob
        //         .sync("./src/blocks/**/**/*style.scss")
        //         .map((path) => "./" + path.replace("./", "")),
        // ],

        // Public CSS
        "public/css/copy-to-clipboard-frontend": glob
            .sync("./dev/scss/frontend.scss")
            .map((path) => "./" + path.replace("./", "")),

        // // Admin Settings
        "admin/css/copy-to-clipboard-survey": glob
            .sync("./dev/scss/survey.scss")
            .map((path) => "./" + path.replace("./", "")),

        // Admin Settings (with jQuery Code)
        "admin/css/copy-to-clipboard-admin": glob
            .sync("./dev/scss/sdk.scss")
            .map((path) => "./" + path.replace("./", "")),

        // "css/gutenberg-editor": glob
        //     .sync("./dev/scss/gutenberg-editor.scss")
        //     .map((path) => "./" + path.replace("./", "")),

        // "js/copy-to-clipboard-settings": glob
        //     .sync("./dev/admin/index.js")
        //     .map((path) => "./" + path.replace("./", "")),

        // "js/copy-to-clipboard": glob.sync("./dev/copy-to-clipboard.js").map((path) => "./" + path.replace("./", "")),

        "admin/js/builder": glob
            .sync("./dev/js/builder/builder.js")
            .map((path) => "./" + path.replace("./", "")),
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader",
                },
            },
            // {
            //     test: /\.(png|jpg|gif|eot|ttf|woff|svg)$/,
            //     loader: 'url?limit=10000&name=/Presentation/_dist/images/[name].[ext]',
            //     exclude: /(\/fonts)/
            // },
            // {
            //     test: /.(otf|eot|ttf|woff|woff2|svg)(\?\S*)?$/,
            //     loader: "file-loader",
            //     exclude: /fonts/,
            //     options: {
            //         //     // publicPath: "/fonts/",
            //         name: "./[path][name].[ext]",
            //         emitFile: false,
            //     },
            // },

            {
                test: /\.(png|svg|jpg|jpeg|gif)$/i,
                type: "asset/resource",
            },
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: "css-loader",
                        options: {
                            sourceMap: !isProduction,
                            url: false,
                            importLoaders: 1,
                        },
                    },
                    {
                        loader: "postcss-loader",
                        options: {
                            postcssOptions: {
                                ident: "postcss",
                                sourceMap: !isProduction,
                                plugins: ["postcss-preset-env"],
                            },
                        },
                    },
                    {
                        loader: "sass-loader",
                        options: {
                            sourceMap: !isProduction,
                        },
                    },
                ],
            },
        ],
    },
    resolve: {
        extensions: [".css", ".scss"],
    },
    output: {
        // Webpack will create js files even though they are not used
        filename: "[name].min.js",
        // filename: "[name].useless.js",
        // chunkFilename: "[name].[chunkhash].useless.js",

        // Where the CSS is saved to
        path: path.resolve(__dirname, "assets"),
        publicPath: "/assets",
    },
    plugins: [
        new UnminifiedWebpackPlugin(),
        new MiniCssExtractPlugin({
            filename: "[name].min.css",
            chunkFilename: "[id].css",
        }),
        // new CopyWebpackPlugin([{ from: "dev/admin/images", to: "images" }]),
        // new CopyWebpackPlugin({
        //     patterns: [{ from: "dev/admin/images", to: "images" }],
        //     options: {
        //         concurrency: 100,
        //     },
        // }),
        new RemovePlugin({
            after: {
                test: [
                    {
                        folder: "assets",
                        method: (absoluteItemPath) => {
                            // return new RegExp(/\.useless.js$/, 'm').test(absoluteItemPath);
                            return new RegExp(/\-editor.js|index.css$/, "m").test(absoluteItemPath);
                        },
                        recursive: false,
                        // beforeRemove: (absoluteFoldersPaths, absoluteFilesPaths) => {
                        //  // cancel removing if there at least one `.txt` file.
                        //  for (const item of absoluteFilesPaths) {
                        //      if (item.includes('index.js')) {
                        //          return true;
                        //      }
                        //  }
                        // },
                    },
                    {
                        folder: "assets/admin/css",
                        method: (absoluteItemPath) => {
                            // return new RegExp(/\.useless.js$/, 'm').test(absoluteItemPath);
                            return new RegExp(/\.js$/, "m").test(absoluteItemPath);
                        },
                        recursive: true,
                    },
                    {
                        folder: "assets/public/css",
                        method: (absoluteItemPath) => {
                            // return new RegExp(/\.useless.js$/, 'm').test(absoluteItemPath);
                            return new RegExp(/\.js$/, "m").test(absoluteItemPath);
                        },
                        recursive: true,
                    },
                ],
            },
        }),
    ],
};

if (!isProduction) {
    delete defaultConfig.devtool; // This will disable generating .map file
}

module.exports = [defaultConfig, assetsConfig];
module.exports.parallelism = 1;
