<?php
/**
 * fris - smart commerce extensions for Magento
 *
 * @category  Fris
 * @package   Fris_SocialButtons
 * @copyright Copyright (c) 2015 fris IT (http://fris.technology)
 * @license   http://fris.technology/license
 * @author    fris IT <support@fris.technology>
 */
class Fris_Socialbuttons_Block_Socialbuttons extends Mage_Core_Block_Template
{
    /**
     * Constructor sets template to use.
     */
    public function __construct()
    {
        parent::__construct();
        if ($currentProduct = Mage::registry('current_product')) {
            $this->setCurrentProduct($currentProduct);
            // Can we pull in fonts and CSS here?
            $this->setTemplate('fris/socialbuttons/socialbuttons_product.phtml');
        }
    }

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
  
        $currentProduct = $this->getCurrentProduct();

        $productImage = $this->helper('catalog/image')->init($currentProduct, 'image')
            ->keepAspectRatio(TRUE)
            ->keepFrame(FALSE);
        $this->setProductImage($productImage);

        $productName = $this->helper('catalog/output')->productAttribute($currentProduct, $currentProduct->getName(), 'name');
        $this->setProductTitle($productName);

        $price = Mage::helper('core')->currency($currentProduct->getPrice());
        $this->setProductPrice($price);
    }

    /**
     * Collects product info and prepares buttons for display via template.
     *
     * @return array, indexed by social network name.
     */
    public function getSocialButtonsInfo()
    {
        $coreHelper = Mage::helper('core/url');
        $site = $coreHelper->getHomeUrl();
        $url = urlencode($coreHelper->getCurrentUrl());
        $imageUrl = urlencode($this->getProductImage());
        $description = urlencode(strip_tags($this->getProductTitle() . ' - ' . $this->getProductPrice()));
        $twitterHandle = $this->getTwitterHandle();
        // URL encoding: %2F=/, %26=&, %3A=:, %3D=equal, %3F=? %5B=[ %5D=]
        $socialButtons = array(
          'facebook' => array(
            // Does facebook ignore p[title], p[summary] etc. except p[url] ?
            'share_url' => "https://www.facebook.com/sharer/sharer.php?p[url]=$url&p[title]=$description&p[images][0]=$imageUrl",
            'tooltip' => $this->__('Share on facebook'),
          ),
          'google_plus' => array(
            'share_url' => "https://plus.google.com/share?url=$url",
            'tooltip' => $this->__('Share on Google+'),
          ),
          'linkedin' => array(
            // https://developer.linkedin.com/docs/share-on-linkedin
            'share_url' => "https://www.linkedin.com/shareArticle?url=$url&title=$description&source=$imageUrl&summary=$site&mini=true",
            'tooltip' => $this->__('Share on LinkedIn'),
          ),
          'pinterest' => array(
            'share_url' => "https://www.pinterest.com/pin/create/button?url=$url&media=$imageUrl&description=$description",
            'tooltip' => $this->__('Share on Pinterest'),
          ),
          'twitter' => array(
            'share_url' => "https://twitter.com/intent/tweet?url=$url&screen_name=$twitterHandle&text=$description",
            'tooltip' => $this->__('Share on Twitter'),
          ),
          'twitter_follow' => array(
            'share_url' => "https://twitter.com/intent/follow?url=$url&screen_name=$twitterHandle",
            'tooltip' => $this->__('Follow us on Twitter'),
            'button_label' => '&nbsp;' . $this->__('follow'),
          ),
        );
        return $socialButtons;
    }

    public function getTwitterHandle()
    {
        $twitterHandle = Mage::getStoreConfig('design/socialbuttons/twitter_handle');
        if (empty($twitterHandle)) {
            return 'screen_name';
        }
        return substr($twitterHandle, 0, 1) === '@'
            ? substr($twitterHandle, 1)
            : $twitterHandle;
    }
}
