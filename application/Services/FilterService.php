<?php

namespace NhatHoa\App\Services;
use NhatHoa\Framework\Abstract\Service;

class FilterService extends Service
{
    private $query;
    private $request;

    public function __construct($query, $request)
    {
        $this->query = $query;
        $this->request = $request;
    }

    public function filter()
    {
        $this->filterColors();
        $this->filterSizes();
        $this->filterPrices();
        $this->filterAttributes();
    }

    protected function filterColors()
    {
        if($this->request->hasQuery("color")){
            $colors = (array) $this->request->query("color");
            $this->query->where(function($query) use($colors){
                foreach($colors as $index => $color){
                    if($index === 0){
                        $query->where("pcl.color_name","like","%{$color}%");
                    }else{
                        $query->orWhere("pcl.color_name","like","%{$color}%");
                    }
                }
            });
        }
    }

    protected function filterSizes()
    {
        if($this->request->hasQuery("size")){
            $sizes = (array) $this->request->query("size");
            $this->query->where(function($query) use ($sizes){
                foreach($sizes as $index => $size){
                    if(count($sizes) === 1){
                        $query->where(function($query) use($size){
                            $query->where("ps.size","like","%{$size}%")
                                ->where("ps.stock",">",0);
                        });
                        $query->orWhere(function($query) use($size){
                             $query->where("pcs.size","like","%{$size}%")
                                ->where("pcs.stock",">",0);
                        });
                    }else{
                        $query->orWhere(function($query) use ($size){
                            $query->where(function($query) use($size){
                                $query->where("ps.size","like","%{$size}%")
                                    ->where("ps.stock",">",0);
                            });
                            $query->orWhere(function($query) use($size){
                                $query->where("pcs.size","like","%{$size}%")
                                    ->where("pcs.stock",">",0);
                            });
                        });        
                    }        
                }
            });
        }
    }

    protected function filterPrices()
    {
        if($this->request->hasQuery("price")){
            $prices = (array) $this->request->query("price");
            $this->query->where(function($query) use ($prices){
                foreach($prices as $index => $price){
                    $query->orWhere(function($query) use($price){
                        $min = explode("-",$price)[0];
                        $max = explode("-",$price)[1];
                        $query->where(function($query) use ($min,$max){
                                $query->where("p.p_price",">=",$min)
                                    ->Where("p.p_price","<=",$max)
                                    ->where("p.p_price",">",0);
                        });
                        $query->orWhere(function($query) use($min,$max){
                            $query->where("pcl.price",">=",$min)
                                    ->Where("pcl.price","<=",$max)
                                    ->where("pcl.price",">",0);
                        });         
                        $query->orWhere(function($query) use($min,$max){
                            $query->where("ps.price",">=",$min)
                                    ->Where("ps.price","<=",$max)
                                    ->where("ps.price",">",0);
                        }); 
                        $query->orWhere(function($query) use($min,$max){
                            $query->where("pcs.price",">=",$min)
                                    ->Where("pcs.price","<=",$max)
                                    ->Where("pcs.price",">",0);
                        });                  
                    });    
                }
            });
        }
    }

    protected function filterAttributes()
    {
        $queryData = $this->request->getData();
        $attributes = array_filter(
            $queryData,
            function($key) {
                return str_starts_with($key, 'attribute');
            },
            ARRAY_FILTER_USE_KEY
        );
        foreach($attributes as $values){
            if(is_array($values)){
                $this->query->where(function($query) use($values){
                    foreach($values as $index => $value){
                        if($index == 0){
                            $query->whereJsonContains('p.attr_values', $value);
                        }else{
                            $query->orWhereJsonContains('p.attr_values', $value);
                        }
                    }
                });
            }
        }
    }
}