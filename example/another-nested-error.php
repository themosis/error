<?php

use Themosis\Components\Error\AdditionalInformation;
use Themosis\Components\Error\InformationGroup;
use Themosis\Components\Error\TextInfo;

class ServiceException extends Exception implements AdditionalInformation
{
    public function information(): InformationGroup
    {
        $info = new InformationGroup(
            name: 'Card IO',
        );

        $info->add(new TextInfo('Transaction #', 'PAY-965653'));
        $info->add(new TextInfo('Error #', 'bank-transaction-failed'));

        return $info;
    }
}

try {
    throw new ServiceException('Service not available.');
} catch (Exception $e) {
    throw new RuntimeException("Third-party service not available.", previous: $e);
}

