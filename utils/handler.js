'use strict'

import { spawnSync } from 'child_process'
import { parseResponse } from "http-string-parser"
import { resolve } from "path"

let app = JSON.parse('{"host": "lambda.dev", "prefix": "", "https": true}') // lar41-properties

export function handler(event, context, callback){
    context.callbackWaitsForEmptyEventLoop = false

    let requestMethod = event.httpMethod || 'GET'
    let requestBody = event.body || ''
    let serverName = event.headers ? event.headers.Host : app.host
    let requestUri = event.path || ''
    let headers = {}
    let queryParams = ''

    if (event.headers) {
        Object.keys(event.headers).map(function (key) {
            headers['HTTP_' + key.toUpperCase().replace(/-/g, '_')] = event.headers[key]
            headers[key.toUpperCase().replace(/-/g, '_')] = event.headers[key]
        })
    }

    if (event.queryStringParameters) {
        queryParams = Object.keys(event.queryStringParameters).map(function (key) {
            return key + "=" + event.queryStringParameters[key]
        }).join("&")
    }

    var scriptPath = resolve(__dirname + app.prefix + '/public/index.php')

    var proc = spawnSync('./php-cgi', ['-f', scriptPath], {
        env: Object.assign({
            REDIRECT_STATUS: 200,
            REQUEST_METHOD: requestMethod,
            SCRIPT_FILENAME: scriptPath,
            SCRIPT_NAME: '/index.php',
            PATH_INFO: '/',
            SERVER_NAME: serverName,
            SERVER_PROTOCOL: 'HTTP/1.1',
            REQUEST_URI: requestUri,
            QUERY_STRING: queryParams,
            AWS_LAMBDA: true,
            CONTENT_LENGTH: Buffer.byteLength(requestBody, 'utf-8'),
            HTTPS: app.https
        }, headers, process.env),
        input: requestBody
    })

    console.log(proc.stderr.toString('utf-8'))

    var parsedResponse = parseResponse(proc.stdout.toString('utf-8'))

    context.succeed({
        statusCode: parsedResponse.statusCode || 200,
        headers: parsedResponse.headers,
        body: parsedResponse.body
    })
}
