$(window).on ->

    image = $('#image')
    wrap = $('#wrap')

    width = image.width()
    height = image.height()

    offset = wrap.offset()        
    
    newX = 0
    newY = 0

    testScale = 1

    image.click (event) ->
        testScale = if event.ctrlKey then (testScale - 0.4) else (testScale + 0.4) 
        pinch event.clientX, event.clientY, testScale

    window.pinch = (x, y, scale) ->
    
        newWidth = image.width() * scale
        newHeight = image.height() * scale

        # Convert from screen to image coordinates
        x -= offset.left + newX
        y -= offset.top + newY

        newX += -x * (newWidth - width) / newWidth
        newY += -y * (newHeight - height) / newHeight
     
        image.css '-webkit-transform', "scale3d(#{scale}, #{scale}, 1)"         
        wrap.css '-webkit-transform', "translate3d(#{newX}px, #{newY}px, 0)"
        
        width = newWidth
        height = newHeight            
