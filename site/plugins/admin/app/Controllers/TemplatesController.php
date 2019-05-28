<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use function Flextype\Component\I18n\__;

class TemplatesController extends Controller
{
   public function index($request, $response, $args)
   {
       return $this->view->render($response,
                                  'plugins/admin/views/templates/extends/templates/index.html', [
           'menu_item' => 'templates',
           'templates_list' => $this->themes->getTemplates(),
           'partials_list' => $this->themes->getPartials(),
           'links' =>  [
                            'templates' => [
                                'link' => $this->router->pathFor('admin.templates.index'),
                                'title' => __('admin_templates'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ],
            'buttons' => [
                            'templates_create' => [
                                'link' => $this->router->pathFor('admin.templates.add'),
                                'title' => __('admin_create_new_template'),
                                'attributes' => ['class' => 'float-right btn']
                            ],
                        ]
       ]);
   }

   public function add($request, $response, $args)
   {

   }

   public function addProcess($request, $response, $args)
   {

   }

   public function edit($request, $response, $args)
   {

   }

   public function editProcess($request, $response, $args)
   {

   }

   public function rename($request, $response, $args)
   {

   }

   public function renameProcess($request, $response, $args)
   {

   }

   public function deleteProcess($request, $response, $args)
   {

   }

   public function duplicateProcess($request, $response, $args)
   {
       $template_path = PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/templates/' . $request->getParsedBody()['template-id'] . '.html';
       $template_path_new = PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/templates/' . $request->getParsedBody()['template-id'] . '-duplicate-' . date("Ymd_His") . '.html';

       if (Filesystem::copy($template_path, $template_path_new)) {
           $this->flash->addMessage('success', __('admin_message_templates_duplicated'));
       } else {
           $this->flash->addMessage('error', __('admin_message_templates_was_not_duplicated'));
       }

       return $response->withRedirect($this->container->get('router')->pathFor('admin.templates.index'));
   }
}
