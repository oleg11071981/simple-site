<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'common', 'language'];

    /**
     * Контактные данные для футера
     *
     * @var array
     */
    protected $contacts;

    /**
     * Текущий язык сайта
     *
     * @var string
     */
    protected $currentLang;

    /**
     * Модель настроек сайта
     *
     * @var \App\Models\NSiteconfigModel
     */
    protected $settingsModel;

    /**
     * Модель страниц сайта
     *
     * @var \App\Models\NSiteModel
     */
    protected $pagesModel;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Загружаем модели
        $this->settingsModel = new \App\Models\NSiteconfigModel();
        $this->pagesModel = new \App\Models\NSiteModel();

        // Загружаем контакты для футера
        $this->contacts = [
            'email'   => $this->settingsModel->get('Email', ''),
            'phone'   => $this->settingsModel->get('Phone', ''),
            'address' => $this->settingsModel->get('Adress', ''),
        ];

        // Устанавливаем текущий язык
        $this->currentLang = get_lang();
    }
}