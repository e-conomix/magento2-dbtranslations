<?php
declare(strict_types=1);

namespace Economix\DbTranslations\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class TranslationActions extends Column
{
    public const URL_PATH_EDIT = 'ecx_dbtranslations/dbtranslations/edit';
    public const URL_PATH_DELETE = 'ecx_dbtranslations/dbtranslations/delete';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $name = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['key_id'])) {
                continue;
            }

            $string = $this->escaper->escapeHtml((string) ($item['string'] ?? ''));
            $translate = $this->escaper->escapeHtml((string) ($item['translate'] ?? ''));

            $item[$name] = [
                'edit' => [
                    'href' => $this->urlBuilder->getUrl(
                        self::URL_PATH_EDIT,
                        ['key_id' => $item['key_id']]
                    ),
                    'label' => __('Edit'),
                ],
                'delete' => [
                    'href' => $this->urlBuilder->getUrl(
                        self::URL_PATH_DELETE,
                        ['key_id' => $item['key_id']]
                    ),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete "%1"', $translate),
                        'message' => __(
                            'Are you sure you want to delete the translation "%1" → "%2"?',
                            $string,
                            $translate
                        ),
                    ],
                    'post' => true,
                ],
            ];
        }

        return $dataSource;
    }
}
