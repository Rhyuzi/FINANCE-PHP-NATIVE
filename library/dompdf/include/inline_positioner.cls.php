<?php

class Inline_Positioner extends Positioner {

  function __construct(Frame_Decorator $frame) {
    parent::__construct($frame); 
  }
  
  function position() {
    $p = $this->_frame->find_block_parent();

    if ( !$p )
      throw new DOMPDF_Exception("No block-level parent found.  Not good.");

    $f = $this->_frame;
    
    $cb = $f->get_containing_block();
    $line = $p->get_current_line_box();

    $is_fixed = false;
    while($f = $f->get_parent()) {
      if($f->get_style()->position === "fixed") {
        $is_fixed = true;
        break;
      }
    }

    $f = $this->_frame;

    if ( !$is_fixed && $f->get_parent() &&
         $f->get_parent() instanceof Inline_Frame_Decorator &&
         $f->is_text_node() ) {
      
      $min_max = $f->get_reflower()->get_min_max_width();
      if ( $min_max["min"] > ($cb["w"] - $line->left - $line->w - $line->right) ) {
        $p->add_line();
      }
    }
    
    $f->set_position($cb["x"] + $line->w, $line->y);

  }
}
