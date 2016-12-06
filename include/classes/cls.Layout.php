<?php

/**
 * @author: Reinier Gombert
 * @date: 5-dec-2016
 */

abstract class Layout extends DAL
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getHeader()
    {
        return '<img class="img-responsive" src="/images/Weblogo.png" alt="Sonja Aalbers" />';
    }
    
    public function getContent($content)
    {
        return '<div class="container-fluid">' . $content . '</div>';
    }
    
    public function getFooter()
    {
        return '
            <footer class="bd-footer text-muted">
                <div class="container">
                    <ul class="bd-footer-links">
                        <li><a href="https://facebook.com">Facebook</a></li>
                        <li><a href="https://twitter.com/">Twitter</a></li>
                        <li><a href="https://plus.google.com/">Google+</a></li>
                    </ul>
                    <p>Project Muziektherapie</p>
                    <p>Copyright 2016. All rights reserved.</p>
                </div>
            </footer>
        ';
    }
}