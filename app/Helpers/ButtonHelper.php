<?php

namespace App\Helpers;

use App\Models\Model;
use App\Traits\HasDrafts;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Throwable;

class ButtonHelper
{
    /**
     * Render a default link.
     *
     * @param string $text
     * @param string $url
     * @param string|null $icon
     * @param string|null $class
     * @param array $attributes
     * @return $this
     */
    public function action($text, $url, $icon = null, $class = null, $attributes = [])
    {
        return view('helpers::button.action')->with([
            'text' => $text,
            'url' => $url,
            'icon' => $icon,
            'class' => $class,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render a default submit button.
     *
     * @param string $text
     * @param string $url
     * @param string|null $icon
     * @param string|null $class
     * @param string|null $confirm
     * @param array $attributes
     * @return $this
     */
    public function submit($text, $url, $icon = null, $class = null, $confirm = null, $attributes = [])
    {
        return view('helpers::button.submit')->with([
            'text' => $text,
            'url' => $url,
            'icon' => $icon,
            'class' => $class,
            'confirm' => $confirm,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the add button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function addRecord($url, array $attributes = [])
    {
        return view('helpers::button.add_record')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the edit button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function editRecord($url, array $attributes = [])
    {
        return view('helpers::button.edit_record')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the delete button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function deleteRecord($url, array $attributes = [])
    {
        return view('helpers::button.delete_record')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the cancel button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function cancelAction($url, array $attributes = [])
    {
        return view('helpers::button.cancel_action')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the update button.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function updatePage(array $attributes = [])
    {
        return view('helpers::button.update_page')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the filter button.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function filterRecords(array $attributes = [])
    {
        return view('helpers::button.filter_records')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the clear button.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function clearFilters(array $attributes = [])
    {
        return view('helpers::button.clear_filters')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the view button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function viewRecord($url, array $attributes = [])
    {
        return view('helpers::button.view_record')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the download button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function downloadFile($url, array $attributes = [])
    {
        return view('helpers::button.download_file')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the save button view helper.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveRecord(array $attributes = [])
    {
        return view('helpers::button.save_record')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the publish button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function publishDraft($url, array $attributes = [])
    {
        return view('helpers::button.publish_draft')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the publish button.
     *
     * @param string $url
     * @param Model $model
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function publishLimboDraft($url, Model $model, array $attributes = [])
    {
        return view('helpers::button.publish_limbo_draft')->with([
            'url' => $url,
            'model' => $model,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the delete button.
     *
     * @param string $url
     * @param Model $model
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function deleteLimboDraft($url, Model $model, array $attributes = [])
    {
        return view('helpers::button.delete_limbo_draft')->with([
            'url' => $url,
            'model' => $model,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the rollback button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function rollbackRevision($url, array $attributes = [])
    {
        return view('helpers::button.rollback_revision')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the duplicate button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function duplicateRecord($url, array $attributes = [])
    {
        return view('helpers::button.duplicate_record')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the restore button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function restoreRecord($url, array $attributes = [])
    {
        return view('helpers::button.restore_record')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the save elsewhere button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveElsewhere($url, array $attributes = [])
    {
        return view('helpers::button.save_elsewhere')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the save new button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveAsNew($url, array $attributes = [])
    {
        return view('helpers::button.save_new')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render save and stay button.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveAndStay(array $attributes = [])
    {
        return view('helpers::button.save_stay')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the save as draft button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveAsDraft($url, array $attributes = [])
    {
        return view('helpers::button.save_draft')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the deleted button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function deletedRecords($url, array $attributes = [])
    {
        return view('helpers::button.deleted_records')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the limbo drafts button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function draftedRecords($url, array $attributes = [])
    {
        return view('helpers::button.drafted_records')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the back button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function goBack($url, array $attributes = [])
    {
        return view('helpers::button.go_back')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Build the attributes for a button (HTML style).
     *
     * @param array $attributes
     * @return array
     */
    protected static function buildAttributes(array $attributes = [])
    {
        $attr = [];

        foreach ($attributes as $key => $value) {
            $attr[] = $key . '="' . $value . '"';
        }

        return $attr;
    }
}