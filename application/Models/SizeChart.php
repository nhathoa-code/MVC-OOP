<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class SizeChart extends Model
{
    protected $_table = "size_charts";

    public function convert()
    {
        return json_decode($this->chart);
    }

    public function getMeasurements()
    {
        $chart = $this->convert();
        $measurements = $chart[0]->measurements;
        return $measurements;
    }

    public function display()
    { ?>
        <table class="size-chart">
            <tbody>
                <tr>
                    <td class="head-top" style="font-weight: bold">Kích thước</td>
                    <?php foreach($this->getMeasurements() as $item): ?>
                        <td class="head-top"><?php echo $item->name; ?></td>
                    <?php endforeach; ?>    
                </tr>
                <?php foreach($this->convert() as $item): ?>
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