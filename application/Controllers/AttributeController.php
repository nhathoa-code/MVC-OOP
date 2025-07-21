<?php

namespace NhatHoa\App\Controllers;

use NhatHoa\App\Repositories\Interfaces\AttributeRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\CategoryRepositoryInterface;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;

class AttributeController extends Controller
{
    protected $categoryRepository;
    protected $attributeRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository, AttributeRepositoryInterface $attributeRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->attributeRepository = $attributeRepository;
    }

    public function index()
    {
        $categories = $this->categoryRepository->getAll(null);
        $attributes = $this->attributeRepository->getAll();
        return view('admin/attribute/index', ['categories' => $categories,'attributes' => $attributes]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'cats' => 'nullable|array|exists:categories,id'
        ]);
        $this->attributeRepository->store($validated);
        return response()->back()->with('success','Thêm thuộc tính thành công');
    }

    public function editAttribute($id)
    {
        $categories = $this->categoryRepository->getAll(null);
        $attribute = $this->attributeRepository->getById($id);
        if(!$attribute) return;
        return view('admin/attribute/edit_attribute',['categories' => $categories,'attribute'=>$attribute]);
    }

    public function updateAttribute(Request $request,$id)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'cats' => 'nullable|array|exists:categories,id'
        ]);
        $attribute = $this->attributeRepository->getById($id);
        if(!$attribute) return;
        $this->attributeRepository->update($attribute,$validated);
        return response()->redirect('admin/attribute')
                        ->with('success','Cập nhật thuộc tính thành công');
    }

    public function deleteAttribute($id)
    {
        $attribute = $this->attributeRepository->getById($id);
        $this->attributeRepository->delete($attribute);
        return response()->redirect('admin/attribute')
            ->with('success','Xóa thuộc tính thành công');
    }

    public function getValues($id)
    {
        $attribute = $this->attributeRepository->getById($id);
        if(!$attribute) return;
        return view('admin/attribute/attribute_values',['attribute'=>$attribute]);
    }

    public function addValue(Request $request, $id)
    {
        $attribute = $this->attributeRepository->getById($id);
        if(!$attribute) return;
        $validated = $request->validate([
            'value' => 'bail|required|string'
        ]);
        $attribute->addValue($validated['value']);
        return response()->back()->with('success','Thêm giá trị thành công');
    }

    public function editValue($attribute_id,$value_id)
    {
        $attribute = $this->attributeRepository->getById($attribute_id);
        if(!$attribute) return;
        $value = $attribute->getValue($value_id);
        return view('admin/attribute/edit_value',['attribute'=>$attribute,'value'=>$value]);
    }

    public function updateValue(Request $request,$attribute_id,$value_id)
    {
        $attribute = $this->attributeRepository->getById($attribute_id);
        if(!$attribute) return;
        $validated = $request->validate([
            'value' => 'required|string'
        ]);
        $attribute->updateValue($value_id,$validated['value']);
        return response()->redirect("admin/attribute/{$attribute_id}/values")
                        ->with('success','Cập nhật giá trị thành công');
    }

    public function deleteValue($attribute_id,$value_id)
    {
        $attribute = $this->attributeRepository->getById($attribute_id);
        if(!$attribute) return;
        $attribute->deleteValue($value_id);
        return response()->redirect("admin/attribute/{$attribute_id}/values")
                        ->with('success','Xóa giá trị thành công');
    }
}