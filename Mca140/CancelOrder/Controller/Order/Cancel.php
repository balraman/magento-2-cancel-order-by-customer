<?php
 
namespace Mca140\CancelOrder\Controller\Order;
 
use Magento\Sales\Controller\OrderInterface;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
 
class Cancel extends \Magento\Framework\App\Action\Action implements OrderInterface
{
    protected $_resultPageFactory;
    protected $_order;
    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    protected $orderLoader;
    
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context, 
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
        )
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    /**
     * Action for cancel
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $result = $this->_objectManager->get(\Magento\Sales\Controller\AbstractController\OrderLoaderInterface::class)->load($this->getRequest());
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        
        $this->_order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        if($this->_order->canCancel()){
            $this->_order->cancel();
            $this->_order->save();
            $this->messageManager->addSuccess(__('Order has been canceled successfully.'));
        }else{
            $this->messageManager->addError(__('Order cannot be canceled.'));
        }
        return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
    }
}
