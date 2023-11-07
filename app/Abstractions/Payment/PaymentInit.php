<?php

namespace App\Abstractions\Payment;

abstract class PaymentInit extends PaymentConnection
{
    protected array $required_fields = [];

    /**
     * Sets user ID
     *
     * @param integer $value
     * @return $this
     */
    private function setUserId(int $value): self
    {
        $this->required_fields['user_id'] = $value;
        return $this;
    }

    /**
     * Sets user first name
     *
     * @param string $value
     * @return $this
     */
    private function setFirstName(string $value): self
    {
        $this->required_fields['first_name'] = $value;
        return $this;
    }

    /**
     * Sets user last name
     *
     * @param string $value
     * @return $this
     */
    private function setLastName(string $value): self
    {
        $this->required_fields['last_name'] = $value;
        return $this;
    }

    /**
     * Sets user email
     *
     * @param string $value
     * @return $this
     */
    private function setEmail(string $value): self
    {
        $this->required_fields['email'] = $value;
        return $this;
    }

    /**
     * Sets user phone
     *
     * @param string $value
     * @return $this
     */
    private function setPhone(string $value): self
    {
        $this->required_fields['phone'] = $value;
        return $this;
    }

    /**
     * Sets source
     *
     * @param string $value
     * @return $this
     */
    private function setSource(string $value): self
    {
        $this->required_fields['source'] = $value;
        return $this;
    }

    /**
     * Sets country
     *
     * @param string $value
     * @return $this
     */
    private function setCountry(string $value): self
    {
        $this->required_fields['country'] = $value;
        return $this;
    }

    /**
     * Sets currency
     *
     * @param string $value
     * @return $this
     */
    private function setCurrency(string $value): self
    {
        $this->required_fields['currency'] = $value;
        return $this;
    }

    /**
     * Sets amount
     *
     * @param double $value
     * @return $this
     */
    private function setAmount(float $value): self
    {
        $this->required_fields['amount'] = $value;
        return $this;
    }

    /**
     * Sets track ID
     *
     * @param string $value
     * @return $this
     */
    private function setTrackId(string $value): self
    {
        $this->required_fields['track_id'] = $value;
        return $this;
    }

    /**
     * Sets customerEmail
     *
     * @param string $value
     * @return $this
     */
    private function setCustomerEmail(string $value): self
    {
        $this->required_fields['customer_email'] = $value;
        return $this;
    }

    /**
     * Sets language
     *
     * @param string $value
     * @return $this
     */
    private function setLanguage(string $value): self
    {
        $this->required_fields['language'] = $value;
        return $this;
    }

    /**
     * Sets udf1 field
     *
     * @param string $value
     * @return $this
     */
    private function setUdf1(string $value): self
    {
        $this->required_fields['udf1'] = $value;
        return $this;
    }

    /**
     * Sets udf2 field
     *
     * @param string $value
     * @return $this
     */
    private function setUdf2(string $value): self
    {
        $this->required_fields['udf2'] = $value;
        return $this;
    }

    /**
     * Sets udf3 field
     *
     * @param string $value
     * @return $this
     */
    private function setUdf3(string $value): self
    {
        $this->required_fields['udf3'] = $value;
        return $this;
    }

    /**
     * Sets udf4 field
     *
     * @param string $value
     * @return $this
     */
    private function setUdf4(string $value): self
    {
        $this->required_fields['udf4'] = $value;
        return $this;
    }

    /**
     * Sets udf5 field
     *
     * @param string $value
     * @return $this
     */
    private function setUdf5(string $value): self
    {
        $this->required_fields['udf5'] = $value;
        return $this;
    }

    /**
     * Merge passed variables with required fields
     *
     * @param array $data
     * @return array
     */
    protected function mergeData(array $data): array
    {
        return array_merge($this->required_fields, $data);
    }

    /**
     * set passed vaiables to pay function to be global
     *
     * @param array $variables
     * @return self
     */
    public function setVariables(array $variables)
    {
        foreach ($variables as $key => $value) {
            if (array_key_exists($key, $this->required_fields)) {
                $this->{"set" . $this->getMethod($key)}($value);
            }
        }
        return $this;
    }

    /**
     * Get Method
     *
     * @param string $key
     * @return string
     */
    private function getMethod(string $key): string
    {
        switch ($key) {
            case str_contains($key, '_'):
                $key = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                break;
            case str_contains($key, '-'):
                $key = str_replace(' ', '', ucwords(str_replace('-', ' ', $key)));
                break;
            case str_contains($key, ' '):
                $key = str_replace(' ', '', ucwords(str_replace(' ', ' ', $key)));
                break;
            case str_contains($key, '.'):
                $key = str_replace(' ', '', ucwords(str_replace('.', ' ', $key)));
                break;
            case ctype_lower($key[0]):
                $key = ucfirst($key);
                break;
            default:
                break;
        }
        return $key;
    }
}
