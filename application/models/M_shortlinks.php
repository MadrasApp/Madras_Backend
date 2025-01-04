<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_shortlinks extends CI_Model {

    // Save a new short link
    public function saveShortLink($shortCode, $originalUrl)
    {
        $data = [
            'short_code' => $shortCode,
            'original_url' => $originalUrl,
        ];
        return $this->db->insert('short_links', $data);
    }

    // Retrieve original URL by short code
    public function getOriginalUrl($shortCode)
    {
        $this->db->where('short_code', $shortCode);
        return $this->db->get('short_links')->row_array();
    }

    // Increment click count
    public function incrementClickCount($shortCode)
    {
        $this->db->where('short_code', $shortCode);
        $this->db->set('click_count', 'click_count + 1', FALSE);
        return $this->db->update('short_links');
    }

    // Get all short links
    public function getAllShortLinks()
    {
        return $this->db->get('short_links')->result();
    }

    // Delete a short link by ID
    public function deleteShortLink($id)
    {
        return $this->db->delete('short_links', ['id' => $id]);
    }

    // Fetch a specific short link by ID
    public function getShortLinkById($id)
    {
        return $this->db->get_where('short_links', ['id' => $id])->row();
    }

    // Update a short link (if you decide to implement an edit feature later)
    public function updateShortLink($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('short_links', $data);
    }
}
