<?php

class block_activity_modules extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_activity_modules');
    }

    function get_content() {
        global $CFG, $DB, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $course = $this->page->course;

        require_once($CFG->dirroot.'/course/lib.php');

        $modinfo = get_fast_modinfo($course);
        $modfullnames = array();

        $archetypes = array();

        foreach($modinfo->cms as $cm) {
            // Exclude activities which are not visible or have no link (=label)
            if (!$cm->uservisible or !$cm->has_view()) {
                continue;
            }
            if (array_key_exists($cm->modname, $modfullnames)) {
                continue;
            }
            if (!array_key_exists($cm->modname, $archetypes)) {
                $archetypes[$cm->modname] = plugin_supports('mod', $cm->modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
            }
            if ($archetypes[$cm->modname] == MOD_ARCHETYPE_RESOURCE) {
                if (!array_key_exists('resources', $modfullnames)) {
                    $modfullnames['resources'] = get_string('resources');
                }
            } else {
                $modfullnames[$cm->modname] = $cm->modplural;
            }
        }

        collatorlib::asort($modfullnames);

        foreach ($modfullnames as $modname => $modfullname) {
            if ($modname === 'resources') {
                $icon = '<img src="'.$OUTPUT->pix_url('f/html') . '" class="icon" alt="" />&nbsp;';
                $this->content->items[] = '<a href="'.$CFG->wwwroot.'/course/resources.php?id='.$course->id.'">'.$icon.$modfullname.'</a>';
            } else {
                $icon = '<img src="'.$OUTPUT->pix_url('icon', $modname) . '" class="icon" alt="" />&nbsp;';
                $this->content->items[] = '<a href="'.$CFG->wwwroot.'/mod/'.$modname.'/index.php?id='.$course->id.'">'.$icon.$modfullname.'</a>';
            }
        }

        return $this->content;
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'my' => false, 'admin' => false,
                     'tag' => false);
    }
}


