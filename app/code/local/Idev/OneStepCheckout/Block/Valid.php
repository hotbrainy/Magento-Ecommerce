<?php
/**
 *    OneStepCheckout main block
 *    @author Jone Eide <mail@onestepcheckout.com>
 *    @copyright Jone Eide <mail@onestepcheckout.com>
 *
 */

class Idev_OneStepCheckout_Block_Valid extends Mage_Core_Block_Template
{
    public function _toHtml()
    {
        $helper = Mage::helper('onestepcheckout/checkout');
        $message = false;

        if($helper->canRun(false)) {
            return '';
        }

        if($helper->canRun(true)) {
            return base64_decode('PGRpdiBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgZ3JleTsgcGFkZGluZzogNXB4OyBtYXJnaW4tYm90dG9tOiA1cHg7IG1hcmdpbi10b3A6IDVweDsgdGV4dC1hbGlnbjogY2VudGVyIiA+VGhpcyBPbmVTdGVwQ2hlY2tvdXQgaXMgcnVubmluZyBvbiBhIGRldmVsb3BtZW50IHNlcmlhbC4gRG8gbm90IHVzZSB0aGlzIHNlcmlhbCBmb3IgcHJvZHVjdGlvbiBlbnZpcm9ubWVudHMuPC9kaXY+');
        }

        return str_replace('[DOMAIN]', $_SERVER['SERVER_NAME'],  base64_decode('PGRpdiBzdHlsZT0iYm9yZGVyOiAzcHggc29saWQgcmVkOyBwYWRkaW5nOiA1cHg7IG1hcmdpbi1ib3R0b206IDE1cHg7IG1hcmdpbi10b3A6IDE1cHg7Ij5QbGVhc2UgZW50ZXIgYSB2YWxpZCBzZXJpYWwgZm9yIHRoZSBkb21haW4gIltET01BSU5dIiBpbiB5b3VyIGFkbWluaXN0cmF0aW9uIHBhbmVsLiBJZiB5b3UgZG9uJ3QgaGF2ZSBvbmUsIHBsZWFzZSBwdXJjaGFzZSBhIHZhbGlkIGxpY2Vuc2UgZnJvbSA8YSBocmVmPSJodHRwOi8vd3d3Lm9uZXN0ZXBjaGVja291dC5jb20iPnd3dy5vbmVzdGVwY2hlY2tvdXQuY29tPC9hPjxici8+PGJyLz5JZiB5b3UgaGF2ZSBlbnRlcmVkIGEgdmFsaWQgc2VyaWFsLCBwbGVhc2Ugc2VlIG91ciA8YSBocmVmPSJodHRwOi8vd3d3Lm9uZXN0ZXBjaGVja291dC5jb20vd2lraS9pbmRleC5waHAvU2VyaWFsX25vdF93b3JraW5nIj53aWtpIHBhZ2U8L2E+IGZvciB0cm91Ymxlc2hvb3Rpbmcgc2VyaWFsIGlzc3Vlcy48L2Rpdj4='));
    }
}

