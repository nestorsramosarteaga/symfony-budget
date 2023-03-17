<?php

namespace App\Custom;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Response;

class Calculations
{
    
    const STORAGE_FEE = 100;
    const MIN_BASIC_FEE = 10;
    const MAX_BASIC_FEE = 50;
    const PERCENTAGE_BASIC_FEE = 0.1;

    /**
    * @param float $vehiclePrice Vehicle's price.
    * @return float The Calculate of Basic Fee
    */
    public function calculateBasicFee($vehiclePrice = null) :int
    {
        $basicFee = round( $vehiclePrice * self::PERCENTAGE_BASIC_FEE, 2 );
        if ( $basicFee == 0 ) return $basicFee;
        $basicFee = max($basicFee, self::MIN_BASIC_FEE);
        $basicFee = min($basicFee, self::MAX_BASIC_FEE);
        return  $basicFee;
    }    


    /**
    * @param float $vehiclePrice Vehicle's price.
    * @return float The Calculate of Association Fee
    */
    public function calculateAssociationFee($vehiclePrice):int
    {
        $vehiclePrice = round($vehiclePrice,2);
        if ( round($vehiclePrice,2) < 1) {
        return 0;
        } elseif ($vehiclePrice >= 1 && $vehiclePrice <= 500) {
        return 5;
        } elseif ($vehiclePrice <= 1000) {
        return 10;
        } elseif ($vehiclePrice <= 3000) {
        return 15;
        } else {
        return 20;
        }
    }


    /**
     * @param float $vehiclePrice Vehicle's price.
    * @return float The Calculate Seller's Special Fee
    */
    public function calculateSellersSpecialFee($vehiclePrice):float
    {
        $specialFee = round($vehiclePrice * 0.02 , 2);
        return $specialFee;
    }


    /**
     * @return float The minimum value fee to be paid for a vehicle
    */
    public function minValueFee() :int
    {
        return self::STORAGE_FEE + self::MIN_BASIC_FEE;
    }


    /**
    * @param float $budget Budget's total amount.
    * @return float Validate the minimum value to be paid for fees
    */
    public function validateMinValue( $budget ) :bool
    {
        if ( $budget > self::minValueFee() )
            return true;

        return false;
        
    }


    /**
    * Function to calculate the maximum value of the vehicle that can be purchased
    *
    * @param float $budget Budget's total amount.
    * @return float The maximum value of the vehicle that can be purchased
    */
    public function calculateMaxValueVehicle(float $budget):array|bool
    {
        $budget = round($budget, 2);
        if ( ! self::validateMinValue( $budget ) ){
            return self::getResponsewithMinimum($budget);
        }            
        
        $vehiclePriceLimit = $budget - self::minValueFee();
        for ($maxVehicleAmount = $vehiclePriceLimit; $maxVehicleAmount > 0; $maxVehicleAmount -= 0.01){
            $storageFee = self::STORAGE_FEE;
            $basicFee = self::calculateBasicFee($maxVehicleAmount);
            $associationFee = self::calculateAssociationFee($maxVehicleAmount);
            $specialFee = self::calculateSellersSpecialFee($maxVehicleAmount);
            $totalFees = $storageFee + $basicFee + $associationFee + $specialFee;
            $maxVehicleAmountWithFees = round($maxVehicleAmount + $totalFees, 2);
            if ( $maxVehicleAmountWithFees <= $budget ) {
                return Array(
                    'maxVehicleAmount' =>round($maxVehicleAmount, 2),
                    'storageFee' => $storageFee,
                    'basicFee' => $basicFee,
                    'specialFee' => $specialFee,
                    'associationFee' => $associationFee,
                    'budget' => $budget,
                );
            }
        }

        return false;

    }
    

    public function getResponsewithMinimum($budget):array
    {
        $maxVehicleAmount = 0;
        $storageFee = 0;
        $basicFee = self::calculateBasicFee($maxVehicleAmount);
        $specialFee = self::calculateSellersSpecialFee($maxVehicleAmount);
        $associationFee = self::calculateAssociationFee($maxVehicleAmount);

        return Array(
            'maxVehicleAmount' => $maxVehicleAmount,
            'storageFee' => $storageFee,
            'basicFee' => $basicFee,
            'specialFee' => $specialFee,
            'associationFee' => $associationFee,
            'budget' => $budget,
        );
    }

}