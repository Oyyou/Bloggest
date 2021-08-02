<?php

class Image
{
    public $id;
    public $value;

    public function __construct($id, $value)
    {
        $this->id = $id;
        $this->value = $value;
    }
}

function utf8ize($d)
{
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string($d)) {
        return utf8_encode($d);
    }
    return $d;
}

?>

<script>
    $(document).ready(function() {
        <?php if (isset($componentList)) : ?>
            <?php foreach ($componentList as $component) : ?>

                var data = JSON.parse(`<?php echo json_encode(($component)); ?>`);
                addComponent(data);

            <?php endforeach; ?>
        <?php endif; ?>
    })

    const addComponent = (component) => {

        const getButtonControl = (a, b, f) => {

            const removeButton = document.createElement('div');
            removeButton.className = "button " + b;

            const buttonContent = document.createElement('p');
            buttonContent.innerHTML = a;

            buttonContent.addEventListener('click', (e) => {
                e.stopPropagation();
                f(e.target.parentNode.parentNode.parentNode);
            });

            removeButton.appendChild(buttonContent);
            removeButton.addEventListener('click', (e) => {
                f(e.target.parentNode.parentNode);
            });

            return removeButton;
        }

        let id = createUUID();

        const parentDiv = document.createElement('div');
        parentDiv.className = "form-addtional component";
        parentDiv.id = id;

        const controlsDiv = document.createElement('div');
        controlsDiv.className = "form-controls";

        const childDiv = document.createElement("div");
        childDiv.className = "component-body"

        const imageElement = document.createElement("img");
        imageElement.src = component ? component.content : "";

        const imageInput = document.createElement('input');
        imageInput.type = "file";
        imageInput.accept = "image/*";
        imageInput.name = "images[]";
        imageInput.id = "image-" + id;
        imageInput.style = "display:none;";

        imageInput.addEventListener('change', (e) => {
            var reader = new FileReader();
            reader.onload = function() {
                imageElement.src = reader.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        })

        const imageLabel = document.createElement('label');
        imageLabel.htmlFor = imageInput.id;
        imageLabel.innerHTML = "Select image";
        imageLabel.style = "cursor:pointer; width:fit-content";

        childDiv.appendChild(imageElement);
        childDiv.appendChild(imageInput);
        childDiv.appendChild(imageLabel);

        const removeButton = getButtonControl('X', 'remove', (value) => {
            document.getElementById("component-list").removeChild(value);
        })

        const moveUpButton = getButtonControl("↑", "move-up", (value) => {

            const parent = value.parentNode;
            const collection = parent.children;

            let obj;
            for (let i = 0; i < collection.length; i++) {
                const current = collection[i];

                if (current.id === value.id) {
                    if (i === 0) {
                        return;
                    } else {
                        break;
                    }
                }

                obj = current;
            }
            parent.insertBefore(value, obj);
        });

        const moveDownButton = getButtonControl("↓", "move-down", (value) => {

            const parent = value.parentNode;
            const collection = parent.children;

            let obj;
            for (let i = collection.length - 1; i > 0; i--) {
                const current = collection[i];

                if (current.id === value.id) {
                    if (i === collection.length - 1) {
                        return;
                    } else {
                        break;
                    }
                }

                obj = current;
            }
            parent.insertBefore(obj, value);
        });

        controlsDiv.appendChild(moveUpButton);
        controlsDiv.appendChild(moveDownButton);
        controlsDiv.appendChild(removeButton);
        parentDiv.appendChild(controlsDiv);
        parentDiv.appendChild(childDiv);

        document.getElementById("component-list").appendChild(parentDiv);
    };
</script>

<?php if (isset($blogTitle)) : ?>
    <h1 class="blog-title">
        <?= $blogTitle ?>
    </h1>
<?php endif; ?>
<div class="blog-form-container">
    <div class="form-side-panel">
        <h2>Options:</h2>
        <button id="addImage">Add Image</button>
        <button id="addTextarea">Add Textarea</button>
    </div>
    <form method="post" class="blog-form" id="submit" enctype="multipart/form-data">
        <div class="form-body" id="blog-form">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required="required" value="<?= (isset($title) ? $title : "") ?>">

            <label for="shortDescription">Short description:</label>
            <input type="text" id="shortDescription" name="shortDescription" value="<?= (isset($shortDescription) ? $shortDescription : "") ?>">

            <div>
                <label class="full-row" for="body">Body:</label>
                <textarea class="full-row" id="body" name="body"><?= (isset($body) ? $body : "") ?></textarea>
            </div>

            <label for="tags">Tags (split by comma):</label>
            <input type="text" id="tags" name="tags" value="<?= (isset($tags) ? $tags : "") ?>" ?>

            <div id="component-list" class="component-list">

            </div>

            <input id="submit" class="button" type="submit" name="submit" value="Save">
        </div>
    </form>
</div>

<script>
    $("#addImage").click(() => addComponent(null));
</script>