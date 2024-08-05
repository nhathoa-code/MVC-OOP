<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class SizeChart extends Model
{
    protected $_table = "size_charts";

    public function getList($page, $limit, $keyword)
    {
        if($keyword){
            $query = $this->where("name","like","%{$keyword}%");
        }else{
            $query = $this->orderBy("id","desc");
        }
        $number_of_size_charts = $query->count(false);
        $size_charts = $query->limit($limit)->offset(($page - 1) * $limit)->get();
        return array($size_charts,$number_of_size_charts);
    }

    public function getSizeChart($id)
    {
        $size_chart = $this->first(where:array("id"=>$id));
        return $size_chart;
    }

    public function getSizeChartForProduct($product_id)
    {
        $size_chart_id = $this->table("product_size_chart_link")
                            ->where("product_id",$product_id)
                            ->first("size_chart_id");
        return $this->first(where:array("id"=>$size_chart_id));
    }

    public function decodeSizeChart()
    {
        return json_decode($this->chart);
    }

    public function getMeasurements()
    {
        $chart = $this->decodeSizeChart();
        $measurements = $chart[0]->measurements;
        return $measurements;
    }

    public function saveSizeChart($validated)
    {
        $this->name = $validated['name'];
        $this->chart = $validated['size-chart'];
        $this->save();
    }

    public function updateSizeChart($validated)
    {
        $this->saveSizeChart($validated);
    }

    public function deleteSizeChart()
    {
        $this->delete();
    }

    public function drawSizeChart()
    { ?>
        <table class="size-chart">
            <tbody>
                <tr>
                    <td class="head-top" style="font-weight: bold">Kích thước</td>
                    <?php foreach($this->getMeasurements() as $item): ?>
                        <td class="head-top"><?php echo $item->name; ?></td>
                    <?php endforeach; ?>    
                </tr>
                <?php foreach($this->decodeSizeChart() as $item): ?>
                    <tr>
                        <td class="size"><?php echo $item->name; ?></td>
                        <?php foreach($item->measurements as $item): ?>
                            <td><?php echo $item->value; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>    
            </tbody>
        </table>
    <?php } 
}