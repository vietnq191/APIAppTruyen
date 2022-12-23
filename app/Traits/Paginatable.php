<?php

namespace App\Traits;

trait Paginatable
{
    protected $perPageMax = 1000;

    /**
     * Get the number of models to return per page.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        $perPage = request('limit', 20);

        if ($perPage === 'all') {
            $perPage = $this->count();
        }

        return max(1, min($this->perPageMax, (int) $perPage));       
    }

    /**
     * @param int $perPageMax
     */
    public function setPerPageMax(int $perPageMax): void
    {
        $this->perPageMax = $perPageMax;
    }

    /**
     * Set the perPage attributes for the model.
     *
     * @param  array  $perPage
     * @return $this
     */
    public function perPage(array $perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }
}