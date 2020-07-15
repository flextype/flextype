<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function array_replace_recursive;

/**
 * Validate folders token
 */
function validate_folders_token($token) : bool
{
    return Filesystem::has(PATH['project'] . '/tokens/folders/' . $token . '/token.yaml');
}

/**
 * Fetch folders(s)
 *
 * endpoint: GET /api/folders
 *
 * Query:
 * path   - [REQUIRED] - Folder path.
 * mode   - [REQUIRED] - Mode.
 * token  - [REQUIRED] - Valid Files token.
 *
 * Returns:
 * An array of folder(s) item objects.
 */
$app->get('/api/folders', function (Request $request, Response $response) use ($flextype, $api_sys_messages) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $path  = $query['path'];
    $mode  = $query['mode'];
    $token = $query['token'];

    if ($flextype['registry']->get('flextype.settings.api.folders.enabled')) {

        // Validate delivery token
        if (validate_folders_token($token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/folders/' . $token. '/token.yaml';

            // Set delivery token file
            if ($folders_token_file_data = $flextype['serializer']->decode(Filesystem::read($folders_token_file_path), 'yaml')) {
                if ($folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Create folders array
                $folders = [];

                // Get list if folder or fodlers for specific folder
                if ($mode == 'collection') {
                    $folders = $flextype['media_folders']->fetchCollection($path);
                } elseif ($mode == 'single') {
                    $folders = $flextype['media_folders']->fetchSingle($path);
                }

                // Write response data
                $response_data['data'] = $folders;

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                Filesystem::write($folders_token_file_path, $flextype['serializer']->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1]), 'yaml'));

                if ($response_code == 404) {

                    // Return response
                    return $response
                           ->withJson($api_sys_messages['NotFound'], $response_code);
                }

                // Return response
                return $response
                       ->withJson($response_data, $response_code);
            }

            return $response
                   ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
        }

        return $response
               ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
    }

    return $response
           ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
});


/**
 * Create folder
 *
 * endpoint: PUT /api/folders
 *
 * Body:
 * path          - [REQUIRED] - New folder path.
 * token         - [REQUIRED] - Valid Entries token.
 * access_token  - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the file object for the file that was just created.
 */
$app->post('/api/folders', function (Request $request, Response $response) use ($flextype, $api_sys_messages) {

  // Get Post Data
  $post_data = $request->getParsedBody();

  // Set variables
  $token        = $post_data['token'];
  $access_token = $post_data['access_token'];
  $path         = $post_data['path'];

  if ($flextype['registry']->get('flextype.settings.api.files.enabled')) {

      // Validate files and access token
      if (validate_files_token($token) && validate_access_token($access_token)) {
          $files_token_file_path = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';
          $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

          // Set files and access token file
          if (($files_token_file_data = $flextype['serializer']->decode(Filesystem::read($files_token_file_path), 'yaml')) &&
              ($access_token_file_data = $flextype['serializer']->decode(Filesystem::read($access_token_file_path), 'yaml'))) {

              if ($files_token_file_data['state'] === 'disabled' ||
                  ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])) {
                  return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
              }

              if ($access_token_file_data['state'] === 'disabled' ||
                  ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                  return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
              }

              // Create folder
              $create_folder = $flextype['media_folders']->create($path);

              if ($create_folder) {
                  $response_data['data'] = $flextype['media_folders']->fetch($path);
              } else {
                  $response_data['data'] = [];
              }

              // Set response code
              $response_code = ($create_folder) ? 200 : 404;

              // Return response
              return $response
                     ->withJson($response_data, $response_code);

              // Update calls counter
              Filesystem::write($files_token_file_path, $flextype['serializer']->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1]), 'yaml'));

              if ($response_code == 404) {

                  // Return response
                  return $response
                         ->withJson($api_sys_messages['NotFound'], $response_code);
              }

              // Return response
              return $response
                     ->withJson($response_data, $response_code);
          }

          return $response
                 ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
      }

      return $response
             ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
  }

  return $response
         ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
});

/**
 * Rename folder
 *
 * endpoint: PUT /api/folders
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the file.
 * new_id        - [REQUIRED] - New Unique identifier of the file.
 * token         - [REQUIRED] - Valid Entries token.
 * access_token  - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the file object for the file that was just created.
 */
$app->put('/api/folders', function (Request $request, Response $response) use ($flextype, $api_sys_messages) {

  // Get Post Data
  $post_data = $request->getParsedBody();

  // Set variables
  $token        = $post_data['token'];
  $access_token = $post_data['access_token'];
  $id           = $post_data['id'];
  $new_id       = $post_data['new_id'];

  if ($flextype['registry']->get('flextype.settings.api.files.enabled')) {

      // Validate files and access token
      if (validate_files_token($token) && validate_access_token($access_token)) {
          $files_token_file_path = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';
          $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

          // Set files and access token file
          if (($files_token_file_data = $flextype['serializer']->decode(Filesystem::read($files_token_file_path), 'yaml')) &&
              ($access_token_file_data = $flextype['serializer']->decode(Filesystem::read($access_token_file_path), 'yaml'))) {

              if ($files_token_file_data['state'] === 'disabled' ||
                  ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])) {
                  return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
              }

              if ($access_token_file_data['state'] === 'disabled' ||
                  ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                  return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
              }

              // Rename folder
              $rename_folder = $flextype['media_folders']->rename($id, $new_id);

              if ($rename_folder) {
                  $response_data['data'] = $flextype['media_folders']->fetch($new_id);
              } else {
                  $response_data['data'] = [];
              }

              // Set response code
              $response_code = ($rename_folder) ? 200 : 404;

              // Return response
              return $response
                     ->withJson($response_data, $response_code);

              // Update calls counter
              Filesystem::write($files_token_file_path, $flextype['serializer']->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1]), 'yaml'));

              if ($response_code == 404) {

                  // Return response
                  return $response
                         ->withJson($api_sys_messages['NotFound'], $response_code);
              }

              // Return response
              return $response
                     ->withJson($response_data, $response_code);
          }

          return $response
                 ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
      }

      return $response
             ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
  }

  return $response
         ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
});

/**
* Delete file
*
* endpoint: DELETE /api/files
*
* Body:
* id           - [REQUIRED] - Unique identifier of the file.
* token        - [REQUIRED] - Valid Entries token.
* access_token - [REQUIRED] - Valid Authentication token.
*
* Returns:
* Returns an empty body with HTTP status 204
*/
$app->delete('/api/folders', function (Request $request, Response $response) use ($flextype) {

  // Get Post Data
  $post_data = $request->getParsedBody();

  // Set variables
  $token        = $post_data['token'];
  $access_token = $post_data['access_token'];
  $id           = $post_data['id'];

  if ($flextype['registry']->get('flextype.settings.api.files.enabled')) {

      // Validate files and access token
      if (validate_files_token($token) && validate_access_token($access_token)) {
          $files_token_file_path = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';
          $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

          // Set files and access token file
          if (($files_token_file_data = $flextype['serializer']->decode(Filesystem::read($files_token_file_path), 'yaml')) &&
              ($access_token_file_data = $flextype['serializer']->decode(Filesystem::read($access_token_file_path), 'yaml'))) {

              if ($files_token_file_data['state'] === 'disabled' ||
                  ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])) {
                  return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
              }

              if ($access_token_file_data['state'] === 'disabled' ||
                  ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                  return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
              }

              // Delete folder
              $delete_folder = $flextype['media_folders']->delete($id);

              // Set response code
              $response_code = ($delete_folder) ? 204 : 404;

              // Update calls counter
              Filesystem::write($files_token_file_path, $flextype['serializer']->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1]), 'yaml'));

              if ($response_code == 404) {

                  // Return response
                  return $response
                         ->withJson($api_sys_messages['NotFound'], $response_code);
              }

              // Return response
              return $response
                     ->withJson($delete_file, $response_code);
          }

          return $response
                 ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
      }

      return $response
             ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
  }

  return $response
         ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
});
